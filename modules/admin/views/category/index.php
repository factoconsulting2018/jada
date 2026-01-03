<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categorías';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nueva Categoría', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
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
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status == \app\models\Category::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
                    },
                    'filter' => [
                        \app\models\Category::STATUS_ACTIVE => 'Activo',
                        \app\models\Category::STATUS_INACTIVE => 'Inactivo',
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
                                'data-confirm' => '¿Está seguro que desea eliminar esta categoría?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

