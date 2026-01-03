<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\QuotationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Cotizaciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nueva Cotización', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'admin-table'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'product_id',
                    'label' => 'Producto',
                    'value' => function ($model) {
                        return $model->product ? $model->product->name : 'N/A';
                    },
                ],
                [
                    'attribute' => 'full_name',
                    'label' => 'Cliente',
                ],
                'email:email',
                'whatsapp',
                [
                    'attribute' => 'id_type',
                    'label' => 'Tipo ID',
                    'value' => function ($model) {
                        return $model->getIdTypeLabel();
                    },
                    'filter' => [
                        'fisico' => 'Físico',
                        'juridico' => 'Jurídico',
                    ],
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Estado',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                    'filter' => [
                        1 => 'Nueva',
                        2 => 'Procesada',
                    ],
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Fecha',
                    'format' => 'datetime',
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
                                'data-confirm' => '¿Está seguro que desea eliminar esta cotización?',
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

