<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/session.php';
require_once __DIR__ . '/../../src/includes/helpers.php';

iniciarSesionSegura();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
}

$entrada = leerJsonEntrada();

// 1) Protección
if (!validarTokenCsrf($entrada['csrf'] ?? null)) {
    responderJson(403, ['ok' => false, 'mensaje' => 'Token de seguridad inválido. Recargá la página.']);
}

// 2) Lectura y normalización de los datos
$nombre    = trim((string)($entrada['nombre'] ?? ''));
$apellido  = trim((string)($entrada['apellido'] ?? ''));
$correo    = trim((string)($entrada['correo'] ?? ''));
$password  = (string)($entrada['password'] ?? '');
$confirmar = (string)($entrada['confirmar'] ?? '');

// 3) Validaciones de servidor. Se acumulan los errores para responder todos juntos
$errores = [];

if ($nombre === '' || mb_strlen($nombre) > 60) {
    $errores[] = 'El nombre es obligatorio (máx. 60 caracteres).';
}
if ($apellido === '' || mb_strlen($apellido) > 60) {
    $errores[] = 'El apellido es obligatorio (máx. 60 caracteres).';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 120) {
    $errores[] = 'Ingresá un correo válido.';
}
if (strlen($password) < 8) {
    $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
}
// Comparación estricta (===) entre ambas passwords
if ($password !== $confirmar) {
    $errores[] = 'Las contraseñas no coinciden.';
}

if ($errores) {
    // 422 = entidad no procesable (datos inválidos)
    responderJson(422, ['ok' => false, 'mensaje' => implode(' ', $errores)]);
}

try {
    $pdo = obtenerConexion();

    // Hash de la contraseña con Bcrypt. cost 12 equilibra seguridad y velocidad.
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    //  INSERT preparado. No se hace SELECT previo: confiamos en la restricción
    //    UNIQUE de MySQL, que evita la condición de carrera entre dos altas simultáneas.
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, apellido, correo, password_hash)
         VALUES (:nombre, :apellido, :correo, :hash)'
    );
    $stmt->execute([
        ':nombre'   => $nombre,
        ':apellido' => $apellido,
        ':correo'   => $correo,
        ':hash'     => $hash,
    ]);

    responderJson(201, ['ok' => true, 'mensaje' => 'Usuario creado. Redirigiendo al login…', 'redirect' => 'index.php']);
} catch (PDOException $ex) {
    // SQLSTATE 23000 = violación de integridad (acá: correo duplicado por UNIQUE).
    if ($ex->getCode() === '23000') {
        responderJson(409, ['ok' => false, 'mensaje' => 'Ese correo ya está registrado.']);
    }
    error_log('[registro] ' . $ex->getMessage());
    responderJson(500, ['ok' => false, 'mensaje' => 'Error interno del servidor.']);
}
