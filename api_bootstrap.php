<?php
declare(strict_types=1);

/**
 * Utilitários compartilhados para endpoints JSON.
 */

function sendJson(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function initApiHeaders(array $allowedMethods = ['GET']): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: ' . implode(', ', array_merge($allowedMethods, ['OPTIONS'])));
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    // Responder a requisições OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function requireMethod(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        sendJson([
            'sucesso' => false,
            'mensagem' => 'Método não permitido'
        ], 405);
    }
}

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);

    if (!is_array($data)) {
        sendJson([
            'sucesso' => false,
            'mensagem' => 'JSON inválido na requisição'
        ], 400);
    }

    return $data;
}

function requireAuth(): void
{
    if (!isset($_SESSION['usuario_id'])) {
        sendJson([
            'sucesso' => false,
            'mensagem' => 'Não autenticado'
        ], 401);
    }
}

function requireAdmin(): void
{
    requireAuth();

    if (($_SESSION['usuario_tipo'] ?? null) !== 'admin') {
        sendJson([
            'sucesso' => false,
            'mensagem' => 'Acesso negado. Apenas administradores.'
        ], 403);
    }
}

