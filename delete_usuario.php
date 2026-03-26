<?php
declare(strict_types=1);

require_once 'api_bootstrap.php';
require_once 'conexao.php';

initApiHeaders(['POST']);
startSecureSession();
requireMethod('POST');
requireAdmin();

$dados = getJsonInput();
if (empty($dados['id_usuario'])) {
    sendJson(['sucesso' => false, 'mensagem' => 'Campo id_usuario é obrigatório'], 400);
}

$id = (int)$dados['id_usuario'];

try {
    $pdo = obterConexao();
    $stmt = $pdo->prepare('DELETE FROM usuario WHERE id_usuario = :id');
    $stmt->execute(['id' => $id]);

    sendJson(['sucesso' => true, 'mensagem' => 'Usuário removido', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro delete_usuario: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao remover usuário'], 500);
}