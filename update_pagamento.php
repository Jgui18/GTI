<?php
declare(strict_types=1);

require_once 'api_bootstrap.php';
require_once 'conexao.php';

initApiHeaders(['POST']);
startSecureSession();
requireMethod('POST');
requireAdmin();

$dados = getJsonInput();
if (empty($dados['id_pagamento'])) {
    sendJson(['sucesso' => false, 'mensagem' => 'Campo id_pagamento é obrigatório'], 400);
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
    sendJson(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar'], 400);
}

// validação de email
if (isset($params['email'])) {
    $email = filter_var(trim($params['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(['sucesso' => false, 'mensagem' => 'E-mail inválido'], 400);
    }
    $params['email'] = $email;
}

try {
    $pdo = obterConexao();
    $sql = 'UPDATE pagamentos SET ' . implode(', ', $fields) . ' WHERE id_pagamento = :id_pagamento';
    $stmt = $pdo->prepare($sql);
    $params['id_pagamento'] = $id;
    $stmt->execute($params);

    sendJson(['sucesso' => true, 'mensagem' => 'Pagamento atualizado', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_pagamento: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar pagamento'], 500);
}