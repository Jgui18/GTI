<?php
declare(strict_types=1);

require_once __DIR__ . '/../api_bootstrap.php';
require_once __DIR__ . '/../conexao.php';

initApiHeaders(['GET']);
startSecureSession();
requireMethod('GET');
requireAdmin();

try {
    $pdo = obterConexao();
    $stmt = $pdo->query(
        "SELECT id_usuario, nome_completo, email, tipo_usuario, data_cadastro
         FROM usuario
         ORDER BY data_cadastro DESC"
    );

    $usuarios = $stmt->fetchAll();

    sendJson([
        'sucesso' => true,
        'total' => count($usuarios),
        'usuarios' => $usuarios
    ]);
} catch (PDOException $e) {
    error_log('Erro api/usuarios: ' . $e->getMessage());
    sendJson([
        'sucesso' => false,
        'mensagem' => 'Erro interno ao listar usuários'
    ], 500);
}

