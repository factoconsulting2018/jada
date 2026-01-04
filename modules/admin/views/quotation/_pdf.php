<?php

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */

use yii\helpers\Html;

?>
<div>
    <h1>Cotización #<?= Html::encode($model->id) ?></h1>
    
    <div class="section">
        <h2>Información del Cliente</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold; width: 30%;">Tipo de Identificación:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->getIdTypeLabel()) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Cédula:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->id_number) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Nombre Completo:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->full_name) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Correo Electrónico:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->email) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">WhatsApp:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->whatsapp) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Estado:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->getStatusLabel()) ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Fecha:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= date('d/m/Y H:i', $model->created_at) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Productos Cotizados</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Producto</td>
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold; text-align: center;">Cantidad</td>
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold; text-align: right;">Precio Unitario</td>
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold; text-align: right;">Subtotal</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->quotationProducts as $qp): ?>
                <tr>
                    <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($qp->product->name) ?></td>
                    <td style="padding: 5pt; border: 1px solid #ddd; text-align: center;"><?= $qp->quantity ?></td>
                    <td style="padding: 5pt; border: 1px solid #ddd; text-align: right;">₡<?= number_format($qp->price, 2, '.', ',') ?></td>
                    <td style="padding: 5pt; border: 1px solid #ddd; text-align: right;">₡<?= number_format($qp->getSubtotal(), 2, '.', ',') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f5f5f5; font-weight: bold;">
                    <td colspan="3" style="padding: 5pt; border: 1px solid #ddd; text-align: right; font-size: 12pt;">Total:</td>
                    <td style="padding: 5pt; border: 1px solid #ddd; text-align: right; font-size: 14pt;">₡<?= number_format($model->getTotal(), 2, '.', ',') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Contact Information at the end -->
    <div class="section" style="margin-top: 40pt; padding-top: 20pt; border-top: 1px solid #ddd;">
        <div style="background-color: #4caf50; color: white; padding: 15pt; border-radius: 4pt; text-align: center;">
            <div style="font-size: 10pt; margin-bottom: 8pt; line-height: 1.6;">
                Contáctenos: +506 4710-1005 | WhatsApp: +506 6060-7309 | San Ramón, Costa Rica.
            </div>
            <div style="font-size: 9pt; line-height: 1.6;">
                www.multiserviciosdeoccidente.com | ventas@multiserviciosdeoccidente.com
            </div>
        </div>
    </div>
</div>

