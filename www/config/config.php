<?php
// Configuraciones generales del proyecto

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1); // esto habilita el log

error_reporting(E_ALL);

// Log de errores
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/mi_error_log.log'); // Ruta al archivo de log

// Constantes generales si querés usar
define('APP_NAME', 'Cuestionario PHP');
define('BASE_PATH', dirname(__DIR__));