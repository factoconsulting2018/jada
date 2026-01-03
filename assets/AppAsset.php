<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
        'https://fonts.googleapis.com/icon?family=Material+Icons',
        'https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css',
        'css/site.css',
    ];
    public $js = [
        'https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js',
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}

