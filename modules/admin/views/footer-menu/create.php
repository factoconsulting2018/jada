<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\FooterMenuItem $model */

$this->title = 'Nuevo Item del Menú';
$this->params['breadcrumbs'][] = ['label' => 'Menú del Footer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="footer-menu-item-create">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

