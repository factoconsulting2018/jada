<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db;dbname=' . (getenv('MYSQL_DATABASE') ?: 'tienda_online'),
    'username' => getenv('MYSQL_USER') ?: 'tienda_user',
    'password' => getenv('MYSQL_PASSWORD') ?: 'tienda_password',
    'charset' => 'utf8mb4',
    
    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

