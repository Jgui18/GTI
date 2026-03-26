<?php
declare(strict_types=1);
/**
 * Arquivo para processar autenticação/login de usuários
 * Verifica credenciais e inicia sessão PHP
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
        session_regenerate_id(true);

        // Atualiza hash de senha quando algoritmo/custo estiver desatualizado
        if (password_needs_rehash($usuario['senha'], PASSWORD_DEFAULT)) {
            $novoHash = password_hash($senha, PASSWORD_DEFAULT);
            $updateSenha = $pdo->prepare("UPDATE usuario SET senha = :senha WHERE id_usuario = :id_usuario");
            $updateSenha->execute([
                'senha' => $novoHash,
                'id_usuario' => $usuario['id_usuario']
            ]);
        }

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
        
        sendJson([
            'sucesso' => true,
            'mensagem' => 'Login bem-sucedido!',
            'usuario' => [
                'id' => $usuario['id_usuario'],
                'nome' => $usuario['nome_completo'],
                'email' => $usuario['email'],
                'tipoUsuario' => $usuario['tipo_usuario'] ?? null
            ]
        ], 200);
    } else {
        // Não revela se o email existe ou não (segurança)
        sendJson([
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos'
        ], 401);
    }
    
} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    sendJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ], 500);
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    sendJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno do servidor. Tente novamente mais tarde.'
    ], 500);
}
?>