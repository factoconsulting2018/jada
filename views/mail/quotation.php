<?php
/** @var app\models\Quotation $quotation */
/** @var app\models\Product $product */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #6750A4;">Nueva Solicitud de Cotización</h2>
    
    <h3>Información del Cliente</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Tipo de Identificación:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($quotation->getIdTypeLabel()) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Cédula:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($quotation->id_number) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Nombre Completo:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($quotation->full_name) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Email:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($quotation->email) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">WhatsApp:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($quotation->whatsapp) ?></td>
        </tr>
    </table>

    <h3>Información del Producto</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Producto:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($product->name) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Precio:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($product->formattedPrice) ?></td>
        </tr>
        <?php if ($product->category): ?>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Categoría:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;"><?= Html::encode($product->category->name) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <p style="margin-top: 20px; color: #666;">
        Fecha de solicitud: <?= date('d/m/Y H:i', $quotation->created_at) ?>
    </p>

    <p style="margin-top: 20px;">
        <a href="<?= Url::to(['/admin/quotation/view', 'id' => $quotation->id], true) ?>" 
           style="background-color: #6750A4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
            Ver Cotización Completa
        </a>
    </p>
</div>

