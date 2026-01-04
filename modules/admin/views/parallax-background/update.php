<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ParallaxBackground $model */

$this->title = 'Actualizar Fondo Parallax: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fondos Parallax', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="parallax-background-update">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>


