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

        <h2 style="margin-top: 2rem; margin-bottom: 1rem;">Productos Cotizados</h2>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e0e0e0;">
                        <th style="padding: 0.75rem; text-align: left;">Producto</th>
                        <th style="padding: 0.75rem; text-align: center;">Cantidad</th>
                        <th style="padding: 0.75rem; text-align: right;">Precio Unitario</th>
                        <th style="padding: 0.75rem; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->quotationProducts as $qp): ?>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 0.75rem;">
                                <?= Html::a(Html::encode($qp->product->name), ['/admin/product/view', 'id' => $qp->product_id], ['target' => '_blank']) ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;"><?= $qp->quantity ?></td>
                            <td style="padding: 0.75rem; text-align: right;">₡<?= number_format($qp->price, 2, '.', ',') ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 500;">₡<?= number_format($qp->getSubtotal(), 2, '.', ',') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #e0e0e0;">
                        <td colspan="3" style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.125rem;">Total:</td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                            ₡<?= number_format($model->getTotal(), 2, '.', ',') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

