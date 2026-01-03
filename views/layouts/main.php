<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;
use app\models\Configuration;
use app\models\Page;

AppAsset::register($this);

$siteTitle = Configuration::getValue('site_title', 'Tienda Online');
$footerText = Configuration::getValue('footer_text', '© ' . date('Y') . ' Tienda Online. Todos los derechos reservados.');
$menuPages = Page::getMenuPages();

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title . ($this->title !== $siteTitle ? ' - ' . $siteTitle : '')) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header class="header">
    <div class="header-content">
        <a href="<?= \yii\helpers\Url::to(['/']) ?>" class="logo"><?= Html::encode($siteTitle) ?></a>
        <button class="mobile-menu-toggle" aria-label="Menu">
            <span class="material-icons">menu</span>
        </button>
        <nav>
            <ul class="nav-menu">
                <li><a href="<?= \yii\helpers\Url::to(['/']) ?>">Inicio</a></li>
                <li><a href="<?= \yii\helpers\Url::to(['/products']) ?>">Productos</a></li>
                <?php foreach ($menuPages as $page): ?>
                    <li><a href="<?= \yii\helpers\Url::to(['/page/view', 'slug' => $page->slug]) ?>"><?= Html::encode($page->title) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= \yii\helpers\Url::to(['/admin/login']) ?>">Admin</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<footer class="footer">
    <div class="footer-content">
        <p><?= nl2br(Html::encode($footerText)) ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

