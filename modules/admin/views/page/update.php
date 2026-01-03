<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Page $model */

$this->title = 'Actualizar Página: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Páginas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="page-update">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

