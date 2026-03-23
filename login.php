<?php
/**
 * Arquivo para processar autenticação/login de usuários
 * Verifica credenciais e inicia sessão PHP
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

// Inclui arquivo de conexão
require_once 'conexao.php';

try {
    // Recebe dados do formulário
    $dados = json_decode(file_get_contents('php://input'), true);
    
    // Validação dos campos obrigatórios
    if (empty($dados['email']) || empty($dados['senha'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail e senha são obrigatórios'
        ]);
        exit;
    }
    
    // Sanitização dos dados
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
    
    // Obtém conexão com o banco
    $pdo = obterConexao();
    
    // Busca o usuário pelo email
    $stmt = $pdo->prepare("SELECT id_usuario, nome_completo, email, senha, tipo_usuario FROM usuario WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();
    
    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Inicia sessão
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
        $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'] ?? null;
        
        // Se "lembrar de mim" estiver marcado, define cookie (opcional)
        if (isset($dados['remember']) && $dados['remember'] === true) {
            // Cookie válido por 30 dias
            setcookie('usuario_email', $email, time() + (30 * 24 * 60 * 60), '/');
        }
        
        http_response_code(200);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Login bem-sucedido!',
            'usuario' => [
                'id' => $usuario['id_usuario'],
                'nome' => $usuario['nome_completo'],
                'email' => $usuario['email'],
                'tipoUsuario' => $usuario['tipo_usuario'] ?? null
            ]
        ]);
    } else {
        // Não revela se o email existe ou não (segurança)
        http_response_code(401);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ]);
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>