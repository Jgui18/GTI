<?php
declare(strict_types=1);
/**
 * Arquivo para verificar se o usuário está autenticado
 * Retorna dados do usuário logado via sessão
 */

require_once 'api_bootstrap.php';
initApiHeaders(['GET']);
startSecureSession();

if (isset($_SESSION['usuario_id'])) {
    sendJson([
        'autenticado' => true,
        'usuario' => [
            'id' => $_SESSION['usuario_id'],
            'nome' => $_SESSION['usuario_nome'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'tipoUsuario' => $_SESSION['usuario_tipo'] ?? null
        ]
    ]);
} else {
    sendJson([
        'autenticado' => false,
        'usuario' => null
    ], 401);
}