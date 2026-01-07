<?php

// Función helper para obtener variables de entorno (compatible con PHP-FPM)
function getEnvVar($name, $default = null) {
    // Intentar getenv() primero
    $value = getenv($name);
    if ($value !== false) {
        return $value;
    }
    // Si no está disponible, intentar $_ENV
    if (isset($_ENV[$name])) {
        return $_ENV[$name];
    }
    // Si no está disponible, intentar $_SERVER
    if (isset($_SERVER[$name])) {
        return $_SERVER[$name];
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

