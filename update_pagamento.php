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
if (empty($dados['id_pagamento'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Campo id_pagamento é obrigatório']);
    exit;
}

$id = (int)$dados['id_pagamento'];

$allowed = ['id_usuario','plano','metodo_pagamento','nome_completo','cpf','email','telefone','data_nascimento','valor','parcelamento','status_pagamento','data_pagamento','data_atualizacao'];
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

// validação de email
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
    $sql = 'UPDATE pagamentos SET ' . implode(', ', $fields) . ' WHERE id_pagamento = :id_pagamento';
    $stmt = $pdo->prepare($sql);
    $params['id_pagamento'] = $id;
    $stmt->execute($params);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pagamento atualizado', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_pagamento: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar pagamento']);
}

?>