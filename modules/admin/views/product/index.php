<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nuevo Producto', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'admin-table'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => function ($model) {
                        if ($model->image) {
                            return Html::img($model->imageUrl, ['style' => 'width: 50px; height: 50px; object-fit: cover; border-radius: 4px;']);
                        }
                        return '';
                    },
                    'contentOptions' => ['style' => 'width: 80px;'],
                ],
                'name',
                [
                    'attribute' => 'category_id',
                    'value' => 'category.name',
                    'filter' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->all(), 'id', 'name'),
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return '₡' . number_format($model->price, 2, '.', ',');
                    },
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status == \app\models\Product::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
                    },
                    'filter' => [
                        \app\models\Product::STATUS_ACTIVE => 'Activo',
                        \app\models\Product::STATUS_INACTIVE => 'Inactivo',
                    ],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Acciones',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="material-icons">visibility</span>', $url, ['title' => 'Ver']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="material-icons">edit</span>', $url, ['title' => 'Editar']);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="material-icons">delete</span>', $url, [
                                'title' => 'Eliminar',
                                'data-confirm' => '¿Está seguro que desea eliminar este producto?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>

