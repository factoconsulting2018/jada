<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - Administración</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Panel Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= \yii\helpers\Url::to(['/admin']) ?>" class="nav-link">
                    <span class="material-icons">dashboard</span> Dashboard
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/product']) ?>" class="nav-link">
                    <span class="material-icons">inventory_2</span> Productos
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/category']) ?>" class="nav-link">
                    <span class="material-icons">category</span> Categorías
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/banner']) ?>" class="nav-link">
                    <span class="material-icons">image</span> Banners
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/parallax-background']) ?>" class="nav-link <?= $this->context->id == 'parallax-background' ? 'active' : '' ?>">
                    <span class="material-icons">wallpaper</span> Fondos Parallax
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/client']) ?>" class="nav-link">
                    <span class="material-icons">people</span> Clientes
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/quotation']) ?>" class="nav-link">
                    <span class="material-icons">request_quote</span> Cotizaciones
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/page']) ?>" class="nav-link">
                    <span class="material-icons">description</span> Páginas
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/footer-menu']) ?>" class="nav-link">
                    <span class="material-icons">menu</span> Menú Footer
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/configuration']) ?>" class="nav-link">
                    <span class="material-icons">build</span> Configuración
                </a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= \yii\helpers\Url::to(['/admin/login/logout']) ?>" class="nav-link">
                <span class="material-icons">logout</span> Cerrar Sesión
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="user-info">
                <?= Yii::$app->user->identity->username ?>
            </div>
        </header>

        <div class="admin-content">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-error">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </main>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

