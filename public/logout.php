<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/config/session.php';

iniciarSesionSegura();

// Solo se cierra sesión con POST y token CSRF válido.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarTokenCsrf($_POST['csrf'] ?? null)) {
    // 1) Vacía los datos de sesión en memoria.
    $_SESSION = [];

    // 2) Expira la cookie en el navegador usando los mismos parámetros con que se creó.
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $p['path'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'],
    ]);

    // 3) Destruye la sesión en el servidor.
    session_destroy();
}

// En cualquier caso se redirige al login.
header('Location: index.php');
exit;
