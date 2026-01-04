<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Client $model */

$this->title = 'Editar Cliente: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="client-update">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

