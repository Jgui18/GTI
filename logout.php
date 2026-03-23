<?php
/**
 * Arquivo para fazer logout do usuário
 * Destrói a sessão PHP
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Destrói a sessão
session_unset();
session_destroy();

// Remove cookie de "lembrar de mim" se existir
if (isset($_COOKIE['usuario_email'])) {
    setcookie('usuario_email', '', time() - 3600, '/');
}

echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Logout realizado com sucesso'
]);
?>