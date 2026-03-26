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
    sendJson(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar'], 400);
}

try {
    $pdo = obterConexao();
    $sql = 'UPDATE plano_tipo_cacamba SET ' . implode(', ', $fields) . ' WHERE id_cacamba = :id_cacamba';
    $stmt = $pdo->prepare($sql);
    $params['id_cacamba'] = $id;
    $stmt->execute($params);

    sendJson(['sucesso' => true, 'mensagem' => 'Caçamba atualizada', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_cacamba: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar caçamba'], 500);
}