<?php
/**
 * Arquivo para processar pagamento de planos
 * Armazena informações do pagamento na tabela pagamentos
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Inicia sessão
session_start();

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido'
    ]);
    exit;
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado. Faça login para continuar.'
    ]);
    exit;
}

// Inclui arquivo de conexão
require_once 'conexao.php';

try {
    // Recebe dados do formulário
    $dados = json_decode(file_get_contents('php://input'), true);
    
    // Validação dos campos obrigatórios
    if (empty($dados['plano']) || empty($dados['metodo_pagamento'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Plano e método de pagamento são obrigatórios'
        ]);
        exit;
    }
    
    // Validação do plano
    $plano = strtolower(trim($dados['plano']));
    if (!in_array($plano, ['basico', 'premium'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Plano inválido. Escolha entre básico ou premium.'
        ]);
        exit;
    }
    
    // Define valor do plano
    $valor = ($plano === 'premium') ? 199.00 : 99.00;
    
    // Validação do método de pagamento
    $metodoPagamento = strtolower(trim($dados['metodo_pagamento']));
    if (!in_array($metodoPagamento, ['credit', 'pix', 'boleto'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Método de pagamento inválido'
        ]);
        exit;
    }
    
    // Validação dos dados pessoais
    if (empty($dados['full-name']) || empty($dados['cpf']) || 
        empty($dados['email']) || empty($dados['phone'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Dados pessoais incompletos'
        ]);
        exit;
    }
    
    // Sanitização dos dados
    $idUsuario = $_SESSION['usuario_id'];
    $nomeCompleto = filter_var(trim($dados['full-name']), FILTER_SANITIZE_STRING);
    $cpf = preg_replace('/[^0-9]/', '', trim($dados['cpf'])); // Remove formatação
    $email = filter_var(trim($dados['email']), FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/[^0-9]/', '', trim($dados['phone'])); // Remove formatação
    $dataNascimento = !empty($dados['birthdate']) ? trim($dados['birthdate']) : null;
    $termosAceitos = (isset($dados['terms']) && $dados['terms'] === true) ? 1 : 0;
    
    // Validação de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido'
        ]);
        exit;
    }
    
    // Dados do cartão (se método for cartão de crédito)
    $numeroCartao = null;
    $nomeCartao = null;
    $validadeCartao = null;
    $parcelamento = null;
    
    if ($metodoPagamento === 'credit') {
        if (empty($dados['card-number']) || empty($dados['card-name']) || 
            empty($dados['card-expiry']) || empty($dados['card-cvv'])) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados do cartão incompletos'
            ]);
            exit;
        }
        
        // Armazena apenas os últimos 4 dígitos por segurança
        $numeroCartaoCompleto = preg_replace('/[^0-9]/', '', $dados['card-number']);
        $numeroCartao = substr($numeroCartaoCompleto, -4);
        $nomeCartao = filter_var(trim($dados['card-name']), FILTER_SANITIZE_STRING);
        $validadeCartao = trim($dados['card-expiry']);
        $parcelamento = isset($dados['card-installments']) ? intval($dados['card-installments']) : 1;
    }
    
    // Obtém conexão com o banco
    $pdo = obterConexao();
    
    // Insere o pagamento no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO pagamentos (
            id_usuario, plano, metodo_pagamento, numero_cartao, nome_cartao, 
            validade_cartao, parcelamento, nome_completo, cpf, data_nascimento, 
            email, telefone, valor, termos_aceitos, status_pagamento
        ) VALUES (
            :id_usuario, :plano, :metodo_pagamento, :numero_cartao, :nome_cartao,
            :validade_cartao, :parcelamento, :nome_completo, :cpf, :data_nascimento,
            :email, :telefone, :valor, :termos_aceitos, :status_pagamento
        )
    ");
    
    $resultado = $stmt->execute([
        'id_usuario' => $idUsuario,
        'plano' => $plano,
        'metodo_pagamento' => $metodoPagamento,
        'numero_cartao' => $numeroCartao,
        'nome_cartao' => $nomeCartao,
        'validade_cartao' => $validadeCartao,
        'parcelamento' => $parcelamento,
        'nome_completo' => $nomeCompleto,
        'cpf' => $cpf,
        'data_nascimento' => $dataNascimento,
        'email' => $email,
        'telefone' => $telefone,
        'valor' => $valor,
        'termos_aceitos' => $termosAceitos,
        'status_pagamento' => 'pendente' // Inicialmente pendente, pode ser atualizado depois
    ]);
    
    if ($resultado) {
        $idPagamento = $pdo->lastInsertId();
        
        http_response_code(200);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Pagamento processado com sucesso!',
            'id_pagamento' => $idPagamento,
            'plano' => $plano,
            'valor' => $valor
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao processar pagamento'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erro no pagamento: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ]);
} catch (Exception $e) {
    error_log("Erro no pagamento: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>