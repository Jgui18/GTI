<?php
/**
 * Arquivo para verificar se o usuário está autenticado
 * Retorna dados do usuário logado via sessão
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'autenticado' => true,
        'usuario' => [
            'id' => $_SESSION['usuario_id'],
            'nome' => $_SESSION['usuario_nome'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'tipoUsuario' => $_SESSION['usuario_tipo'] ?? null
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'autenticado' => false,
        'usuario' => null
    ]);
}
?>