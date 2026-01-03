<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\FooterMenuItem $model */

$this->title = 'Actualizar Item: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Menú del Footer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="footer-menu-item-update">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

