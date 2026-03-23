<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado']);
    exit;
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
if (empty($dados['id_usuario'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Campo id_usuario é obrigatório']);
    exit;
}

$id = (int)$dados['id_usuario'];

$fields = [];
$params = [];

$allowed = ['nome_completo','email','cpf','telefone','tipo_usuario','termos_aceitos'];
foreach ($allowed as $f) {
    if (isset($dados[$f])) {
        $fields[] = "$f = :$f";
        $params[$f] = $dados[$f];
    }
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar']);
    exit;
}

// validação simples de email
if (isset($params['email'])) {
    $email = filter_var(trim($params['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail inválido']);
        exit;
    }
    $params['email'] = $email;
}

try {
    $pdo = obterConexao();
    $sql = 'UPDATE usuario SET ' . implode(', ', $fields) . ' WHERE id_usuario = :id_usuario';
    $stmt = $pdo->prepare($sql);
    $params['id_usuario'] = $id;
    $stmt->execute($params);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_usuario: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar usuário']);
}

?>