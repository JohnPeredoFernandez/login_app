<?php

declare(strict_types=1);

/**
 * Escapa texto antes de imprimirlo en HTML (defensa contra XSS)
 * convierte comillas simples y dobles; UTF-8 evita trucos de codificación
 */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Envía una respuesta JSON y termina la ejecución
 * El tipo de retorno "never" indica que esta función no devuelve el control.
 */
function responderJson(int $codigoHttp, array $datos): never
{
    http_response_code($codigoHttp);                                  // Código HTTP (200, 401, 422...).
    header('Content-Type: application/json; charset=utf-8');          // Avisa al cliente que es JSON.
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);                 // Conserva tildes y ñ sin escapar.
    exit;                                                             // Corta la ejecución.
}

/**
 * Lee y decodifica el cuerpo JSON de la petición
 * Devuelve un array vacío si el cuerpo no es JSON válido
 */
function leerJsonEntrada(): array
{
    $crudo = file_get_contents('php://input');   // Cuerpo crudo de la petición
    $datos = json_decode($crudo ?: '', true);    // true => array asociativo
    return is_array($datos) ? $datos : [];
}
