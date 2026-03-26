<?php
declare(strict_types=1);
/**
 * Arquivo para fazer logout do usuário
 * Destrói a sessão PHP
 */

require_once 'api_bootstrap.php';
initApiHeaders(['POST']);
startSecureSession();
requireMethod('POST');

// Destrói a sessão
session_unset();
session_destroy();

// Remove cookie de "lembrar de mim" se existir
if (isset($_COOKIE['usuario_email'])) {
    setcookie('usuario_email', '', time() - 3600, '/');
}

sendJson([
    'sucesso' => true,
    'mensagem' => 'Logout realizado com sucesso'
]);