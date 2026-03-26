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
    sendJson(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar'], 400);
}

// validação simples de email
if (isset($params['email'])) {
    $email = filter_var(trim($params['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(['sucesso' => false, 'mensagem' => 'E-mail inválido'], 400);
    }
    $params['email'] = $email;
}

try {
    $pdo = obterConexao();
    $sql = 'UPDATE usuario SET ' . implode(', ', $fields) . ' WHERE id_usuario = :id_usuario';
    $stmt = $pdo->prepare($sql);
    $params['id_usuario'] = $id;
    $stmt->execute($params);

    sendJson(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso', 'rowsAffected' => $stmt->rowCount()]);
} catch (PDOException $e) {
    error_log('Erro update_usuario: ' . $e->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar usuário'], 500);
}