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
if (empty($dados['id_cacamba'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Campo id_cacamba é obrigatório']);
    exit;
}

$id = (int)$dados['id_cacamba'];

$allowed = ['id_plano','tipo_residuo','tamanho','descricao'];
$fields = [];
$params = [];
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

try {
    $pdo = obterConexao();
    $sql = 'UPDATE plano_tipo_cacamba SET ' . implode(', ', $fields) . ' WHERE id_cacamba = :id_cacamba';
    $stmt = $pdo->prepare($sql);
    $params['id_cacamba'] = $id;
    $stmt->execute($params);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Caçamba atualizada', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_cacamba: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar caçamba']);
}

?>