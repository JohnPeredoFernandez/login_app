<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/config/session.php';
require_once __DIR__ . '/../src/includes/helpers.php';

iniciarSesionSegura();

// Guardia de acceso: sin sesión válida se vuelve al login.
if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate');

$csrf     = obtenerTokenCsrf();
// Datos guardados en sesión por api/login.php (valores por defecto por si faltara alguno).
$nombre   = (string)($_SESSION['nombre'] ?? 'usuario');
$apellido = (string)($_SESSION['apellido'] ?? '');
$correo   = (string)($_SESSION['correo'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida | Proyecto Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <main class="tarjeta centrado">

        <div class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($nombre, 0, 1))) ?></div>
        <div class="estado"><i></i>Sesión activa</div>

        <!-- e() escapa el nombre: si alguien se registró con HTML, no se ejecuta. -->
        <h1>¡Bienvenido, <?= e($nombre) ?>!</h1>
        <p class="sub" style="margin-bottom:0">Ingresaste correctamente al sistema.</p>

        <!-- Resumen de la cuenta: lista de definición (dt = dato, dd = valor). Todo escapado con e(). -->
        <dl class="datos">
            <div>
                <dt>Nombre completo</dt>
                <dd><?= e(trim($nombre . ' ' . $apellido)) ?></dd>
            </div>
            <div>
                <dt>Correo</dt>
                <dd><?= e($correo) ?></dd>
            </div>
        </dl>

        <!-- Evita que un tercero cierre tu sesión con un simple enlace -->
        <form action="logout.php" method="post" class="acciones">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <button type="submit" class="secundario">Cerrar sesión</button>
        </form>

        <p class="pie">Proyecto Login · Trabajo Práctico 3</p>
    </main>
</body>

</html>