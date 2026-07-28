<?php
/**
 * Cliente HTTP para la API de usuarios (Flask) de SIGERUTT.
 */
require_once __DIR__ . '/config.php';

function api_request(string $method, string $path, ?array $data = null, ?string $token = null): array {
    $url = API_BASE_URL . $path;

    $headers = ['Content-Type: application/json'];
    if ($token !== null) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'status' => 0, 'body' => null, 'error' => $curlError];
    }

    $body = json_decode($response, true);
    return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $body, 'error' => null];
}

function api_login(string $email, string $password): array {
    return api_request('POST', '/api/usuarios/login', ['email' => $email, 'password' => $password]);
}

function api_admin_crear_usuario(string $token, string $nombre, string $email, string $password, string $role): array {
    return api_request('POST', '/api/admin/usuarios', [
        'nombre' => $nombre,
        'email' => $email,
        'password' => $password,
        'role' => $role,
    ], $token);
}

function api_admin_listar_usuarios(string $token): array {
    return api_request('GET', '/api/admin/usuarios', null, $token);
}

function api_admin_obtener_usuario(string $token, string $email): array {
    return api_request('GET', '/api/admin/usuarios/' . rawurlencode($email), null, $token);
}

function api_admin_actualizar_usuario(string $token, string $emailActual, array $cambios): array {
    return api_request('PUT', '/api/admin/usuarios/' . rawurlencode($emailActual), $cambios, $token);
}

function api_admin_eliminar_usuario(string $token, string $email): array {
    return api_request('DELETE', '/api/admin/usuarios/' . rawurlencode($email), null, $token);
}
