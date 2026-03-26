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

try {
    $pdo = obterConexao();
    $stmt = $pdo->prepare('DELETE FROM pagamentos WHERE id_pagamento = :id');
    $stmt->execute(['id' => $id]);

    sendJson(['sucesso' => true, 'mensagem' => 'Pagamento removido', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro delete_pagamento: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao remover pagamento'], 500);
}