<?php

declare(strict_types=1);

// Rutas relativas a este archivo: subimos un nivel (..) hasta la carpeta src.
require_once __DIR__ . '/../src/config/session.php';
require_once __DIR__ . '/../src/includes/helpers.php';

iniciarSesionSegura();

// Si ya hay un usuario logueado, no tiene sentido mostrar el login.
if (!empty($_SESSION['usuario_id'])) {
    header('Location: bienvenida.php');
    exit;
}

// Token CSRF que viajará oculto en el formulario.
$csrf = obtenerTokenCsrf();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- Viewport: necesario para que el diseño responsive funcione en móviles. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Proyecto Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- el script se ejecuta co HTML. -->
    <script src="assets/js/auth.js" defer></script>
</head>

<body>
    <main class="tarjeta">
        <!-- Cabecera de marca: icono de candado (neutro) + nombre del sistema. -->
        <div class="marca">
            <span class="marca-icono"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="5" y="11" width="14" height="9" rx="2" />
                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                </svg></span>
            <span>Proyecto Login</span>
        </div>

        <h1>Iniciar sesión</h1>
        <p class="sub">Ingresá con tu correo y contraseña.</p>

        <!-- data-formulario le dice a auth.js qué endpoint usar.
             novalidate NO se usa: queremos la validación nativa de "required". -->
        <form id="formAuth" data-formulario="login" data-endpoint="api/login.php">
            <!-- Token CSRF oculto. -->
            <input type="hidden" id="csrf" value="<?= e($csrf) ?>">

            <div class="campo">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" maxlength="120" autocomplete="username" placeholder="nombre@ejemplo.com" required>
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <!-- entrada: contenedor relativo para posicionar el botón "ojo" sobre el input. -->
                <div class="entrada">
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                    <button type="button" class="ojo" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path class="tachado" d="M4 20L20 4" />
                        </svg></button>
                </div>
            </div>

            <button type="submit">Ingresar</button>

            <p id="mensaje" class="mensaje" role="alert" aria-live="polite"></p>
        </form>

        <p class="enlace">¿No tenés cuenta? <a href="registro.php">Crear usuario nuevo</a></p>

        <!-- Pie institucional de la tarjeta. -->
        <p class="pie">Proyecto Login · Trabajo Práctico 3</p>
    </main>
</body>

</html>