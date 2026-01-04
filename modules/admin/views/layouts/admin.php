<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;
use app\models\Quotation;

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
                <li><a href="<?= \yii\helpers\Url::to(['/admin/client']) ?>" class="nav-link">
                    <span class="material-icons">people</span> Clientes
                </a></li>
                <li class="menu-item-has-children">
                    <a href="<?= \yii\helpers\Url::to(['/admin/product']) ?>" class="nav-link menu-toggle">
                        <span class="material-icons">inventory_2</span> Productos
                        <span class="material-icons menu-arrow">chevron_right</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="<?= \yii\helpers\Url::to(['/admin/product']) ?>" class="nav-link">
                            <span class="material-icons">inventory_2</span> Productos
                        </a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/admin/category']) ?>" class="nav-link">
                            <span class="material-icons">category</span> Categorías
                        </a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/admin/brand']) ?>" class="nav-link">
                            <span class="material-icons">branding_watermark</span> Marcas
                        </a></li>
                    </ul>
                </li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/banner']) ?>" class="nav-link">
                    <span class="material-icons">image</span> Banners
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/quotation']) ?>" class="nav-link" style="position: relative;">
                    <span class="material-icons">request_quote</span> Cotizaciones
                    <?php 
                    $pendingCount = Quotation::getPendingCount();
                    if ($pendingCount > 0): 
                    ?>
                        <span class="notification-badge" style="position: absolute; top: 0; right: 0; background: #f44336; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; transform: translate(50%, -50%);">
                            <span class="material-icons" style="font-size: 14px;">notifications</span>
                        </span>
                    <?php endif; ?>
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/page']) ?>" class="nav-link">
                    <span class="material-icons">description</span> Páginas
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/report']) ?>" class="nav-link">
                    <span class="material-icons">assessment</span> Reportes
                </a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/footer-menu']) ?>" class="nav-link">
                    <span class="material-icons">menu</span> Menús
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

<?php
$this->registerCss("
.sidebar-nav .menu-item-has-children {
    position: relative;
}
.sidebar-nav .menu-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}
.sidebar-nav .menu-arrow {
    font-size: 18px;
    transition: transform 0.3s ease;
}
.sidebar-nav .menu-item-has-children.active .menu-arrow {
    transform: rotate(90deg);
}
.sidebar-nav .sub-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    padding-left: 0;
    margin: 0;
}
.sidebar-nav .menu-item-has-children.active .sub-menu {
    max-height: 500px;
}
.sidebar-nav .sub-menu li {
    padding-left: 2rem;
}
.sidebar-nav .sub-menu .nav-link {
    padding-left: 0.5rem;
}
");

$this->registerJs("
(function() {
    var currentUrl = window.location.pathname;
    var menuItems = document.querySelectorAll('.sidebar-nav .menu-item-has-children');
    
    menuItems.forEach(function(menuItem) {
        var subMenu = menuItem.querySelector('.sub-menu');
        var links = subMenu.querySelectorAll('a');
        var isActive = false;
        
        links.forEach(function(link) {
            var linkPath = new URL(link.href).pathname;
            if (currentUrl.indexOf(linkPath) !== -1 && linkPath !== '/admin') {
                isActive = true;
                link.closest('li').classList.add('active');
            }
        });
        
        if (isActive) {
            menuItem.classList.add('active');
        }
        
        var toggle = menuItem.querySelector('.menu-toggle');
        
        if (toggle) {
            // Click on the menu toggle - expand/collapse menu
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                menuItem.classList.toggle('active');
                return false;
            });
        }
    });
})();
", \yii\web\View::POS_READY);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

