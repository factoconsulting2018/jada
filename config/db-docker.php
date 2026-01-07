<?php

// Función helper para obtener variables de entorno (compatible con PHP-FPM)
function getEnvVar($name, $default = null) {
    // Intentar $_ENV primero (más confiable en PHP-FPM)
    if (isset($_ENV[$name]) && $_ENV[$name] !== '') {
        return $_ENV[$name];
    }
    // Intentar $_SERVER (también confiable en PHP-FPM)
    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') {
        return $_SERVER[$name];
    }
    // Intentar getenv() (puede no funcionar en PHP-FPM)
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
    }
    // Intentar leer del archivo .env si existe (último recurso)
    static $envFile = null;
    if ($envFile === null) {
        $envPath = dirname(__DIR__) . '/.env';
        if (file_exists($envPath) && is_readable($envPath)) {
            $envFile = [];
            $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, '#') === 0) {
                        continue; // Saltar líneas vacías y comentarios
                    }
                    if (strpos($line, '=') !== false) {
                        list($key, $val) = explode('=', $line, 2);
                        $key = trim($key);
                        $val = trim($val, " \t\n\r\0\x0B\"'");
                        if (!empty($key) && !empty($val)) {
                            $envFile[$key] = $val;
                        }
                    }
                }
            }
        }
        if ($envFile === null) {
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

