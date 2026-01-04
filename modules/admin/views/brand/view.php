<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Brand $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Marcas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar esta marca?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status == \app\models\Brand::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
                    },
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>

