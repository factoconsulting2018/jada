<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'tienda-online',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-cookie-validation-key-change-this-in-production',
            'csrfParam' => '_csrf-tienda-online',
            // Configuración para HTTPS detrás de proxy
            'trustedHosts' => [
                'multiserviciosdeoccidente.com',
                'www.multiserviciosdeoccidente.com',
            ],
            'secureHeaders' => [
                'X-Forwarded-Proto',
                'X-Forwarded-For',
                'X-Forwarded-Host',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-tienda-online', 'httpOnly' => true],
            'loginUrl' => ['/admin/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                    ],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['yii\db\*'],
                    'logFile' => '@runtime/logs/db.log',
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'admin/login' => 'admin/login/index',
                'admin/logout' => 'admin/login/logout',
                'products' => 'product/index',
                'product/<id:\d+>' => 'product/view',
                'category/<id:\d+>' => 'category/view',
                'page/<slug:[\w-]+>' => 'page/view',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'es_CR',
            'currencyCode' => 'CRC',
            'decimalSeparator' => '.',
            'thousandSeparator' => ',',
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1'],
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1', '::1'],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['bootstrap'][] = 'gii';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

