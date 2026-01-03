<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar este producto?',
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
                'description:ntext',
                [
                    'attribute' => 'price',
                    'value' => '₡' . number_format($model->price, 2, '.', ','),
                ],
                [
                    'attribute' => 'category_id',
                    'value' => $model->category->name,
                ],
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => $model->image ? Html::img($model->imageUrl, ['style' => 'max-width: 300px;']) : 'Sin imagen',
                ],
                [
                    'attribute' => 'status',
                    'value' => $model->status == \app\models\Product::STATUS_ACTIVE ? 'Activo' : 'Inactivo',
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>

