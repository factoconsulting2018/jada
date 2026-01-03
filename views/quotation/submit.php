<?php

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */
/** @var array $products */
/** @var float $total */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\PriceHelper;

$this->title = 'Completar Cotización';
?>
<div class="quotation-submit">
    <div class="container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
        <h1 class="section-title">Completar Cotización</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Products Summary -->
            <div class="products-summary" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Resumen de Productos</h2>
                
                <?php foreach ($products as $item): ?>
                    <div style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e0e0e0; align-items: center;">
                        <div style="flex-shrink: 0;">
                            <?php if ($item['product']->image): ?>
                                <img src="<?= Html::encode($item['product']->imageUrl) ?>" 
                                     alt="<?= Html::encode($item['product']->name) ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span class="material-icons" style="font-size: 24px; color: #ccc;">inventory_2</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="margin: 0; font-size: 0.875rem; font-weight: 500;">
                                <?= Html::encode($item['product']->name) ?>
                            </h3>
                            <p style="margin: 0.25rem 0 0 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.75rem;">
                                Cantidad: <?= $item['quantity'] ?> × <?= Html::encode($item['product']->formattedPrice) ?>
                            </p>
                        </div>
                        <div style="text-align: right; font-weight: 500;">
                            <?= Html::encode('₡' . number_format($item['subtotal'], 2, '.', ',')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="border-top: 2px solid #e0e0e0; margin-top: 1rem; padding-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 500;">Total:</span>
                        <span style="font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                            <?= Html::encode('₡' . number_format($total, 2, '.', ',')) ?>
                        </span>
                    </div>
                    <?php 
                    $dollarTotal = PriceHelper::formatDollars($total);
                    if ($dollarTotal): 
                    ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);">Aprox. en dólares:</span>
                        <span style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);">
                            <?= Html::encode($dollarTotal) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form -->
            <div class="quotation-form-container" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Datos de Contacto</h2>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-error" style="padding: 1rem; margin-bottom: 1rem; background: #ffebee; color: #c62828; border-radius: 4px;">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'id_type')->dropDownList([
                    'fisico' => 'Físico',
                    'juridico' => 'Jurídico',
                ], ['prompt' => 'Seleccione...', 'class' => 'form-control']) ?>

                <?= $form->field($model, 'id_number')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Cédula']) ?>

                <?= $form->field($model, 'full_name')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Nombre completo']) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'class' => 'form-control', 'placeholder' => 'correo@ejemplo.com']) ?>

                <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Número de WhatsApp']) ?>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <?= Html::submitButton('Enviar Cotización', ['class' => 'btn btn-primary', 'style' => 'width: 100%;']) ?>
                    <?= Html::a('Volver al Carrito', ['index'], ['class' => 'btn btn-secondary', 'style' => 'width: 100%; margin-top: 0.5rem;']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .quotation-submit .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

