<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MainMenuItem $model */

$this->title = 'Actualizar Item: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Menús', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="main-menu-item-update">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form-main-menu', [
            'model' => $model,
            'pageList' => $pageList,
        ]) ?>
    </div>
</div>

