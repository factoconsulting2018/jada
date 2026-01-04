<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\ParallaxBackground;

/** @var yii\web\View $this */
/** @var app\models\ParallaxBackgroundSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Fondos Parallax';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parallax-background-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nuevo Fondo Parallax', ['create'], ['class' => 'btn btn-primary']) ?>
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
                            return Html::img($model->imageUrl, ['style' => 'width: 100px; height: 60px; object-fit: cover; border-radius: 4px;']);
                        }
                        return '<span class="material-icons">image</span>';
                    },
                    'contentOptions' => ['style' => 'width: 120px;'],
                ],
                [
                    'attribute' => 'section',
                    'value' => function ($model) {
                        return $model->getSectionLabel();
                    },
                    'filter' => ParallaxBackground::getSections(),
                ],
                'title',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                    'filter' => [
                        ParallaxBackground::STATUS_INACTIVE => 'Inactivo',
                        ParallaxBackground::STATUS_ACTIVE => 'Activo',
                    ],
                ],
                'position',
                'created_at:datetime',
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
                                'data-confirm' => '¿Está seguro que desea eliminar este fondo parallax?',
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

