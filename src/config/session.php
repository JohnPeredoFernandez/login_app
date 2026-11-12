<?php

declare(strict_types=1);

/**
 * Inicia la sesión con cookies endurecidas
 * DEBE llamarse antes de enviar cualquier salida al navegador
 */
function iniciarSesionSegura(): void
{
    // Si ya hay sesión activa no se vuelve a iniciar (evita un warning)
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Rechaza IDs de sesión que el servidor no generó (anti session fixation)
    ini_set('session.use_strict_mode', '1');
    // Impide pasar el ID de sesión por la URL; solo viaja en cookie
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'lifetime' => 0,                     // La cookie muere al cerrar el navegador
        'path'     => '/',                   // Válida para todo el sitio
        'secure'   => !empty($_SERVER['HTTPS']), // "Secure" solo si hay HTTPS (en localhost suele no haber)
        'httponly' => true,                  // JavaScript NO puede leer la cookie (mitiga XSS)
        'samesite' => 'Strict',              // No se envía en peticiones desde otros sitios (mitiga CSRF)
    ]);

    session_start();
}

/**
 * Devuelve el token CSRF de la sesión; lo crea si todavía no existe
 * random_bytes() usa un generador criptográficamente seguro
 */
function obtenerTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
    }
    return $_SESSION['csrf_token'];
}

/**
 * Compara el token recibido con el de la sesión
 */
function validarTokenCsrf(?string $tokenRecibido): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($tokenRecibido)
        && hash_equals($_SESSION['csrf_token'], $tokenRecibido);
}
