<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */

$this->title = 'Cotización #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cotizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar esta cotización?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'product_id',
                    'label' => 'Producto',
                    'value' => function ($model) {
                        return $model->product ? Html::a($model->product->name, ['/admin/product/view', 'id' => $model->product_id]) : 'N/A';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'id_type',
                    'label' => 'Tipo de Identificación',
                    'value' => function ($model) {
                        return $model->getIdTypeLabel();
                    },
                ],
                'id_number',
                'full_name',
                'email:email',
                'whatsapp',
                [
                    'attribute' => 'product_image',
                    'label' => 'Imagen del Producto',
                    'value' => function ($model) {
                        if ($model->product_image) {
                            return Html::img($model->productImageUrl, ['style' => 'max-width: 300px;']);
                        }
                        return 'No disponible';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Estado',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>

