<?php
// Activa el tipado estricto: PHP no convierte tipos "a escondidas", evita bugs sutiles
declare(strict_types=1);

/**
 * Devuelve una única conexión PDO por petición (patrón singleton)
 * Reutilizarla evita abrir varias conexiones a MySQL en un mismo request
 */
function obtenerConexion(): PDO
{
    // "static" conserva el valor entre llamadas dentro de la misma petición
    static $pdo = null;

    // Si ya existe la conexión, se reutiliza
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Parámetros de conexión (en producción irían en variables de entorno)
    $host    = '127.0.0.1';          // IP en vez de "localhost": fuerza TCP y evita problemas de socket
    $puerto  = '3306';               // Puerto por defecto de MySQL
    $base    = 'login_app';          // Base creada en schema.sql
    $usuario = 'login_app_user';     // Usuario de mínimo privilegio
    $clave   = 'Cambiar_Esta_Clave_123!'; // Debe coincidir con la de schema.sql

    // DSN con driver mysql y charset utf8mb4 (requerido por las reglas del proyecto)
    $dsn = "mysql:host=$host;port=$puerto;dbname=$base;charset=utf8mb4";

    $opciones = [
        // Lanza excepciones ante errores SQL en lugar de fallar en silencio
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Los SELECT devuelven arrays asociativos (['correo' => ...]).
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Desactiva la emulación: los prepared statements los ejecuta MySQL de verdad,
        // la defensa real contra SQL Injection
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Crea la conexión; si falla, lanza PDOException (la capturan los endpoints)
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);

    return $pdo;
}
