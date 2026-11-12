<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/config/session.php';
require_once __DIR__ . '/../src/includes/helpers.php';

iniciarSesionSegura();

// Un usuario ya logueado no necesita registrarse.
if (!empty($_SESSION['usuario_id'])) {
    header('Location: bienvenida.php');
    exit;
}

$csrf = obtenerTokenCsrf();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario | Proyecto Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/auth.js" defer></script>
</head>

<body>
    <main class="tarjeta">

        <div class="marca">
            <span class="marca-icono"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="5" y="11" width="14" height="9" rx="2" />
                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                </svg></span>
            <span>Proyecto Login</span>
        </div>

        <h1>Crear usuario nuevo</h1>
        <p class="sub">Completá tus datos para registrarte.</p>

        <form id="formAuth" data-formulario="registro" data-endpoint="api/registro.php">
            <input type="hidden" id="csrf" value="<?= e($csrf) ?>">

            <div class="fila">
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" maxlength="60" autocomplete="given-name" required>
                </div>
                <div class="campo">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" maxlength="60" autocomplete="family-name" required>
                </div>
            </div>

            <div class="campo">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" maxlength="120" autocomplete="email" placeholder="nombre@ejemplo.com" required>
            </div>

            <!-- mínimo 8 caracteres, regla repetida en el servidor. -->
            <div class="campo">
                <label for="password">Contraseña (mínimo 8 caracteres)</label>

                <div class="entrada">
                    <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
                    <button type="button" class="ojo" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path class="tachado" d="M4 20L20 4" />
                        </svg></button>
                </div>
            </div>

            <div class="campo">
                <label for="confirmar">Confirmar contraseña</label>

                <div class="entrada">
                    <input type="password" id="confirmar" name="confirmar" minlength="8" autocomplete="new-password" required>
                    <button type="button" class="ojo" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path class="tachado" d="M4 20L20 4" />
                        </svg></button>
                </div>
            </div>

            <button type="submit">Crear usuario</button>
            <p id="mensaje" class="mensaje" role="alert" aria-live="polite"></p>
        </form>

        <p class="enlace">¿Ya tenés cuenta? <a href="index.php">Volver al login</a></p>

        <!-- Pie institucional de la tarjeta. -->
        <p class="pie">Proyecto Login · Trabajo Práctico 3</p>
    </main>
</body>

</html>