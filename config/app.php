<?php
// =====================================================
// FUNDACITE - Configuración Principal
// =====================================================

// Detectar base URL automáticamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/\\');

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', $protocol . '://' . $host . $basePath);
define('BASE_URL_PATH', $basePath);
define('APP_NAME', 'FUNDACITE Carabobo');
define('APP_VERSION', '1.0.0');

// Zona horaria Venezuela
date_default_timezone_set('America/Caracas');

// Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload de clases
spl_autoload_register(function ($className) {
    $paths = [
        BASE_PATH . '/app/core/' . $className . '.php',
        BASE_PATH . '/app/models/' . $className . '.php',
        BASE_PATH . '/app/controllers/' . $className . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Helpers globales
require_once BASE_PATH . '/app/core/helpers.php';

// Conexión DB
require_once BASE_PATH . '/config/database.php';
