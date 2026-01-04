<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ParallaxBackground $model */

$this->title = 'Nuevo Fondo Parallax';
$this->params['breadcrumbs'][] = ['label' => 'Fondos Parallax', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parallax-background-create">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>


