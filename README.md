# Proyecto Login

**Alumno:** [John Peredo]
**Materia:** [Análisis de Sistemas]
**Año de cursada:** [2026]

## Descripción

Sistema de autenticación con tres pantallas: inicio de sesión, alta de usuario nuevo y pantalla de bienvenida para el usuario logueado. El backend está hecho en PHP 8 con PDO, los datos se guardan en MySQL 8 y el frontend usa HTML, CSS y JavaScript nativos, sin librerías externas.

## Tecnologías

| Capa | Tecnología |
|---|---|
| Servidor web | Apache (XAMPP) |
| Backend | PHP 8 con PDO y consultas preparadas |
| Base de datos | MySQL 8.0+ (no MariaDB) |
| Frontend | HTML5, CSS3 responsive y JavaScript Vanilla (fetch + async/await) |
| Editor | Visual Studio Code |

## Instalación

1. Copiar la carpeta del proyecto a `C:/xampp/htdocs/ProyectoLogin_TP3`.
2. Ejecutar `sql/schema.sql` en MySQL 8 (Workbench o phpMyAdmin). Crea la base `ProyectoLogin_TP3`, la tabla `usuarios` y un usuario con permisos mínimos (solo SELECT e INSERT).
3. Si se cambia la contraseña de `ProyectoLogin_TP3_user` en el script SQL, actualizarla también en `src/config/database.php`.
4. Iniciar Apache en XAMPP y abrir `http://localhost/ProyectoLogin_TP3/public/index.php`.

Nota: el script usa el collation `utf8mb4_0900_ai_ci`, que existe solo en MySQL 8. Con MariaDB hay que reemplazarlo por `utf8mb4_unicode_ci`.

## Estructura del proyecto

| Ruta | Contenido |
|---|---|
| `public/index.php` | Pantalla de login |
| `public/registro.php` | Alta de usuario nuevo |
| `public/bienvenida.php` | Pantalla para el usuario logueado |
| `public/logout.php` | Cierre de sesión (POST con token CSRF) |
| `public/api/login.php` | Endpoint de autenticación (SELECT + sesión) |
| `public/api/registro.php` | Endpoint de alta (INSERT) |
| `public/assets/css/styles.css` | Estilos responsive |
| `public/assets/js/auth.js` | Validación de campos vacíos y coincidencia de contraseñas |
| `src/config/` | Conexión PDO y manejo seguro de sesión (bloqueado desde la web por `.htaccess`) |
| `src/includes/helpers.php` | Funciones auxiliares (escape de HTML, respuestas JSON) |
| `sql/schema.sql` | Script de base de datos |

## Medidas de seguridad

| Medida | Dónde se aplica |
|---|---|
| Consultas preparadas (sin emulación) | Todas las consultas SQL |
| Contraseñas con `password_hash` (bcrypt) y `password_verify` | Alta y login |
| Mensaje de error genérico y verificación con hash señuelo | Login |
| Token CSRF en formularios y endpoints | Login, alta y logout |
| `session_regenerate_id(true)` al autenticar | Login |
| Cookie de sesión HttpOnly y SameSite Strict | Configuración de sesión |
| Escape de salida con `htmlspecialchars` | Páginas PHP |
| Usuario MySQL con permisos mínimos | Base de datos |
