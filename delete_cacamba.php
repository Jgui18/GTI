<?php
declare(strict_types=1);

require_once 'api_bootstrap.php';
require_once 'conexao.php';

initApiHeaders(['POST']);
startSecureSession();
requireMethod('POST');
requireAdmin();

$dados = getJsonInput();
if (empty($dados['id_cacamba'])) {
    sendJson(['sucesso' => false, 'mensagem' => 'Campo id_cacamba é obrigatório'], 400);
}

$id = (int)$dados['id_cacamba'];

try {
    $pdo = obterConexao();
    $stmt = $pdo->prepare('DELETE FROM plano_tipo_cacamba WHERE id_cacamba = :id');
    $stmt->execute(['id' => $id]);

    sendJson(['sucesso' => true, 'mensagem' => 'Caçamba removida', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro delete_cacamba: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao remover caçamba'], 500);
}