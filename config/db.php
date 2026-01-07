<?php

// Función helper para obtener variables de entorno (compatible con PHP-FPM)
function getEnvVar($name, $default = null) {
    // Intentar getenv() primero
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
    }
    // Si no está disponible, intentar $_ENV
    if (isset($_ENV[$name]) && $_ENV[$name] !== '') {
        return $_ENV[$name];
    }
    // Si no está disponible, intentar $_SERVER
    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') {
        return $_SERVER[$name];
    }
    // Intentar leer del archivo .env si existe (último recurso)
    static $envFile = null;
    if ($envFile === null) {
        $envPath = dirname(__DIR__) . '/.env';
        if (file_exists($envPath)) {
            $envFile = [];
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Saltar comentarios
                }
                if (strpos($line, '=') !== false) {
                    list($key, $val) = explode('=', $line, 2);
                    $envFile[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
                }
            }
        } else {
            $envFile = false; // Marcar como no disponible
        }
    }
    if ($envFile !== false && isset($envFile[$name]) && $envFile[$name] !== '') {
        return $envFile[$name];
    }
    // Retornar valor por defecto
    return $default;
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db;dbname=' . getEnvVar('MYSQL_DATABASE', 'tienda_online'),
    'username' => getEnvVar('MYSQL_USER', 'tienda_user'),
    'password' => getEnvVar('MYSQL_PASSWORD', 'tienda_password'),
    'charset' => 'utf8mb4',
    
    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];