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

try {
    $pdo = obterConexao();
    $stmt = $pdo->prepare('DELETE FROM usuario WHERE id_usuario = :id');
    $stmt->execute(['id' => $id]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário removido', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro delete_usuario: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao remover usuário']);
}

?>