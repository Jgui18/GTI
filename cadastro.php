<?php
declare(strict_types=1);
/**
 * Arquivo para processar cadastro de novos usuários
 * Valida email único e armazena senha com hash seguro
 */

require_once 'api_bootstrap.php';
initApiHeaders(['POST']);
startSecureSession();
requireMethod('POST');

// Inclui arquivo de conexão
require_once 'conexao.php';

try {
    // Recebe dados do formulário
    $dados = getJsonInput();
    
    // Validação dos campos obrigatórios
    $camposObrigatorios = ['nome', 'email', 'senha'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($dados[$campo])) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => "O campo {$campo} é obrigatório"
            ]);
            exit;
        }
    }
    
    // Sanitização dos dados
    $nome = trim($dados['nome']);
    $email = filter_var(trim($dados['email']), FILTER_SANITIZE_EMAIL);
    $senha = $dados['senha'];
    
    // Validação de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail inválido'
        ]);
        exit;
    }
    
    // Validação de senha (mínimo 6 caracteres conforme o formulário)
    if (strlen($senha) < 6) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'A senha deve ter no mínimo 6 caracteres'
        ]);
        exit;
    }
    
    // Obtém conexão com o banco
    $pdo = obterConexao();
    
    // Verifica se o email já existe
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este e-mail já está cadastrado!'
        ]);
        exit;
    }
    
    // Gera hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Prepara dados adicionais (opcionais)
    // Converte strings vazias para NULL
    $cpf = (isset($dados['cpf']) && !empty(trim($dados['cpf']))) ? trim($dados['cpf']) : null;
    $telefone = (isset($dados['telefone']) && !empty(trim($dados['telefone']))) ? trim($dados['telefone']) : null;
    // Converte boolean para inteiro (1 ou 0) para compatibilidade com MySQL TINYINT
    $termosAceitos = (isset($dados['terms']) && $dados['terms'] === true) ? 1 : 0;
    
    // Mapeia tipoUsuario do formulário para os valores ENUM
    $tipoUsuarioForm = isset($dados['tipoUsuario']) ? trim($dados['tipoUsuario']) : null;
    $tipoUsuario = 'cliente'; // padrão
    
    if ($tipoUsuarioForm === 'collector' || $tipoUsuarioForm === 'donor') {
        $tipoUsuario = 'cliente';
    } elseif ($tipoUsuarioForm === 'recycler') {
        $tipoUsuario = 'empresa';
    }
    
    // Insere novo usuário no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO usuario (nome_completo, email, cpf, telefone, senha, tipo_usuario, termos_aceitos)
        VALUES (:nome_completo, :email, :cpf, :telefone, :senha, :tipo_usuario, :termos_aceitos)
    ");
    
    $resultado = $stmt->execute([
        'nome_completo' => $nome,
        'email' => $email,
        'cpf' => $cpf,
        'telefone' => $telefone,
        'senha' => $senhaHash,
        'tipo_usuario' => $tipoUsuario,
        'termos_aceitos' => $termosAceitos
    ]);
    
    if ($resultado) {
        // Obtém o ID do usuário recém-cadastrado
        $usuarioId = $pdo->lastInsertId();
        
        // Processa endereço se fornecido
        if (isset($dados['cep']) && !empty($dados['cep']) && 
            isset($dados['address']) && !empty($dados['address'])) {
            
            // Parse do endereço (formato: "Rua, bairro, cidade/UF")
            $enderecoCompleto = trim($dados['address']);
            $numero = isset($dados['address-number']) ? trim($dados['address-number']) : '';
            $complemento = isset($dados['complemento']) ? trim($dados['complemento']) : null;
            $cep = trim($dados['cep']);
            
            // Tenta extrair cidade e estado do endereço
            // Formato esperado: "Rua, bairro, cidade/UF"
            $partes = explode(',', $enderecoCompleto);
            $logradouro = isset($partes[0]) ? trim($partes[0]) : $enderecoCompleto;
            $bairro = isset($partes[1]) ? trim($partes[1]) : '';
            
            $cidade = '';
            $estado = '';
            if (isset($partes[2])) {
                $cidadeEstado = trim($partes[2]);
                $cidadeEstadoArray = explode('/', $cidadeEstado);
                $cidade = isset($cidadeEstadoArray[0]) ? trim($cidadeEstadoArray[0]) : '';
                $estado = isset($cidadeEstadoArray[1]) ? trim($cidadeEstadoArray[1]) : '';
            }
            
            // Se não conseguiu extrair, usa valores padrão ou do CEP
            if (empty($cidade) || empty($estado)) {
                // Tenta buscar via API do ViaCEP se necessário
                // Por enquanto, usa valores do endereço completo
                $cidade = $cidade ?: 'Não informado';
                $estado = $estado ?: 'RJ'; // padrão
            }
            
            // Insere endereço na tabela endereco
            try {
                $stmtEndereco = $pdo->prepare("
                    INSERT INTO endereco (id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado)
                    VALUES (:id_usuario, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado)
                ");
                
                $stmtEndereco->execute([
                    'id_usuario' => $usuarioId,
                    'cep' => $cep,
                    'logradouro' => $logradouro,
                    'numero' => $numero,
                    'complemento' => $complemento,
                    'bairro' => $bairro ?: 'Não informado',
                    'cidade' => $cidade,
                    'estado' => $estado
                ]);
            } catch (PDOException $e) {
                // Log do erro mas não impede o cadastro
                error_log("Erro ao inserir endereço: " . $e->getMessage());
            }
        }
        
        // Inicia sessão para o usuário recém-cadastrado
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_tipo'] = $tipoUsuario;
        
        session_regenerate_id(true);
        sendJson([
            'sucesso' => true,
            'mensagem' => 'Cadastro realizado com sucesso!',
            'usuario' => [
                'id' => $usuarioId,
                'nome' => $nome,
                'email' => $email
            ]
        ], 201);
    } else {
        throw new Exception('Erro ao inserir usuário no banco de dados');
    }
    
} catch (PDOException $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    sendJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ], 500);
} catch (Exception $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    sendJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ], 500);
}
?>