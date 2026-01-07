<?php

// Función helper para obtener variables de entorno (compatible con PHP-FPM)
function getEnvVar($name, $default = null) {
    // Cargar archivo .env primero (más confiable en PHP-FPM que no pasa variables)
    static $envFile = null;
    if ($envFile === null) {
        $envPath = dirname(__DIR__) . '/.env';
        $envFile = [];
        if (file_exists($envPath) && is_readable($envPath)) {
            $content = @file_get_contents($envPath);
            if ($content !== false) {
                $lines = explode("\n", $content);
                foreach ($lines as $line) {
                    $line = trim($line);
                    // Saltar líneas vacías y comentarios
                    if (empty($line) || strpos($line, '#') === 0) {
                        continue;
                    }
                    // Buscar el primer = que separa clave de valor
                    $pos = strpos($line, '=');
                    if ($pos !== false && $pos > 0) {
                        $key = trim(substr($line, 0, $pos));
                        $val = trim(substr($line, $pos + 1));
                        // Remover comillas si existen
                        if ((substr($val, 0, 1) === '"' && substr($val, -1) === '"') ||
                            (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
                            $val = substr($val, 1, -1);
                        }
                        if (!empty($key) && $val !== '') {
                            $envFile[$key] = $val;
                        }
                    }
                }
            }
        }
    }
    
    // Prioridad 1: Archivo .env (más confiable en PHP-FPM)
    if (isset($envFile[$name]) && $envFile[$name] !== '') {
        return $envFile[$name];
    }
    
    // Prioridad 2: $_ENV (confiable en PHP-FPM si clear_env = no)
    if (isset($_ENV[$name]) && $_ENV[$name] !== '') {
        return $_ENV[$name];
    }
    
    // Prioridad 3: $_SERVER (confiable en PHP-FPM si clear_env = no)
    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') {
        return $_SERVER[$name];
    }
    
    // Prioridad 4: getenv() (puede no funcionar en PHP-FPM)
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
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

