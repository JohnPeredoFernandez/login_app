<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/session.php';
require_once __DIR__ . '/../../src/includes/helpers.php';

iniciarSesionSegura();

// Este endpoint solo acepta POST; cualquier otro verbo recibe 405.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
}

$entrada = leerJsonEntrada();

// 1) si el token no coincide, se rechaza la petición (403 = prohibido)
if (!validarTokenCsrf($entrada['csrf'] ?? null)) {
    responderJson(403, ['ok' => false, 'mensaje' => 'Token de seguridad inválido. Recargá la página.']);
}

// 2) Normalización: el correo se limpia de espacios; la password NO se modifica
//    (los espacios pueden ser parte legítima de una contraseña)
$correo   = trim((string)($entrada['correo'] ?? ''));
$password = (string)($entrada['password'] ?? '');

// 3) Validación de servidor
if ($correo === '' || $password === '') {
    responderJson(422, ['ok' => false, 'mensaje' => 'Completá usuario y contraseña.']);
}

try {
    $pdo = obtenerConexion();

    // 4) Consulta PREPARADA: ":correo" es un marcador, el valor viaja separado del SQL
    $stmt = $pdo->prepare(
        'SELECT id, nombre, apellido, correo, password_hash FROM usuarios WHERE correo = :correo LIMIT 1'
    );
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(); // Array asociativo o false si no existe.


    $hashSenuelo = '$2y$12$usesomesillystringforsalt.eK3hVQ0cQ9m5xv6hZk2w1gq6Qk1Gm';
    $hashAVerificar = $usuario['password_hash'] ?? $hashSenuelo;

    // 5) password_verify compara la clave contra el hash 
    $esValida = password_verify($password, $hashAVerificar);

    if ($usuario === false || !$esValida) {
        // Mensaje genérico a propósito: no revela si falló el correo o la clave
        responderJson(401, ['ok' => false, 'mensaje' => 'Credenciales inválidas.']);
    }

    // 6) Login correcto: se regenera el ID de sesión (anti session fixation).
    session_regenerate_id(true);

    // 7) Se guardan en sesión solo los datos mínimos necesarios
    $_SESSION['usuario_id'] = (int)$usuario['id'];
    $_SESSION['nombre']     = $usuario['nombre'];
    $_SESSION['apellido']   = $usuario['apellido'];
    $_SESSION['correo']     = $usuario['correo'];

    responderJson(200, ['ok' => true, 'mensaje' => 'Ingreso correcto.', 'redirect' => 'bienvenida.php']);
} catch (PDOException $ex) {
    // El detalle técnico va al log del servidor, nunca al cliente
    error_log('[login] ' . $ex->getMessage());
    responderJson(500, ['ok' => false, 'mensaje' => 'Error interno del servidor.']);
}
