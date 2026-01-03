<?php

/** @var yii\web\View $this */
/** @var app\models\Quotation $quotation */
/** @var array $successData */

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\PriceHelper;

$this->title = 'Cotización Enviada';
?>
<div class="quotation-success">
    <div class="container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
        <h1 class="section-title">¡Cotización Enviada con Éxito!</h1>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 2rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="material-icons" style="font-size: 80px; color: #4caf50; margin-bottom: 1rem;">check_circle</span>
                <h2 style="color: #4caf50; margin: 0;">Cotización #<?= $quotation->id ?> Enviada</h2>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem;">
                    Recibirá un correo electrónico con los detalles de su cotización.
                </p>
            </div>

            <div style="border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; padding: 1.5rem 0; margin-bottom: 2rem;">
                <h3 style="margin-top: 0;">Resumen de la Cotización</h3>
                
                <div style="margin-bottom: 1rem;">
                    <p style="margin: 0.5rem 0;"><strong>Nombre:</strong> <?= Html::encode($quotation->full_name) ?></p>
                    <p style="margin: 0.5rem 0;"><strong>Email:</strong> <?= Html::encode($quotation->email) ?></p>
                    <p style="margin: 0.5rem 0;"><strong>WhatsApp:</strong> <?= Html::encode($quotation->whatsapp) ?></p>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="margin-top: 0;">Productos Cotizados</h3>
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
                        <?php foreach ($successData['products'] as $product): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 0.75rem;"><?= Html::encode($product['product_name']) ?></td>
                                <td style="padding: 0.75rem; text-align: center;"><?= $product['quantity'] ?></td>
                                <td style="padding: 0.75rem; text-align: right;">₡<?= number_format($product['price'], 2, '.', ',') ?></td>
                                <td style="padding: 0.75rem; text-align: right; font-weight: 500;">₡<?= number_format($product['subtotal'], 2, '.', ',') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid #e0e0e0;">
                            <td colspan="3" style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.125rem;">Total:</td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                                ₡<?= number_format($successData['total'], 2, '.', ',') ?>
                            </td>
                        </tr>
                        <?php 
                        $dollarTotal = PriceHelper::formatDollars($successData['total']);
                        if ($dollarTotal): 
                        ?>
                        <tr>
                            <td colspan="3" style="padding: 0.25rem 0.75rem; text-align: right; font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);">Aprox. en dólares:</td>
                            <td style="padding: 0.25rem 0.75rem; text-align: right; font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);">
                                <?= Html::encode($dollarTotal) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>

            <div style="text-align: center; padding-top: 2rem; border-top: 1px solid #e0e0e0;">
                <a href="<?= Url::to(['/products']) ?>" class="btn btn-primary" style="margin-right: 1rem;">
                    Ver Más Productos
                </a>
                <a href="<?= Url::to(['/']) ?>" class="btn btn-secondary">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 24px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: var(--md-sys-color-primary);
    color: white;
}

.btn-primary:hover {
    background-color: #5a4290;
}

.btn-secondary {
    background-color: #e0e0e0;
    color: var(--md-sys-color-on-surface);
}

.btn-secondary:hover {
    background-color: #d0d0d0;
}
</style>

