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

                <?php $form = ActiveForm::begin([
                    'id' => 'quotation-submit-form',
                    'options' => ['class' => 'quotation-form'],
                ]); ?>

                <?= $form->field($model, 'id_type')->dropDownList([
                    'fisico' => 'Físico',
                    'juridico' => 'Jurídico',
                ], ['prompt' => 'Seleccione...', 'class' => 'form-control']) ?>

                <?= $form->field($model, 'id_number')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Cédula']) ?>

                <?= $form->field($model, 'full_name')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Nombre completo']) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'class' => 'form-control', 'placeholder' => 'correo@ejemplo.com']) ?>

                <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Número de WhatsApp']) ?>

                <div id="form-errors" style="display: none; padding: 1rem; margin-bottom: 1rem; background: #ffebee; color: #c62828; border-radius: 4px;"></div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <?= Html::submitButton('Enviar Cotización', ['class' => 'btn btn-primary', 'style' => 'width: 100%;', 'id' => 'submit-quotation-btn']) ?>
                    <?= Html::a('Volver al Carrito', ['index'], ['class' => 'btn btn-secondary', 'style' => 'width: 100%; margin-top: 0.5rem;']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Éxito -->
<div id="quotationSuccessModal" class="quotation-success-modal" style="display: none;">
    <div class="quotation-success-modal-content">
        <div class="quotation-success-modal-header">
            <span class="material-icons" style="font-size: 64px; color: #4caf50; margin-bottom: 1rem;">check_circle</span>
            <h2 style="margin: 0; color: #4caf50;">¡Cotización Enviada!</h2>
        </div>
        <div class="quotation-success-modal-body">
            <p style="margin: 0; font-size: 1rem; color: var(--md-sys-color-on-surface);">
                Su cotización fue enviada con éxito.
            </p>
        </div>
        <div class="quotation-success-modal-footer">
            <a href="<?= \yii\helpers\Url::to(['/products']) ?>" class="btn btn-primary" style="flex: 1; text-align: center; text-decoration: none; display: inline-block;">
                Ir a Productos
            </a>
            <button type="button" onclick="document.getElementById('quotationSuccessModal').style.display='none'" class="btn btn-secondary" style="flex: 1; margin-top: 0.5rem;">
                Cerrar
            </button>
        </div>
    </div>
</div>

<style>
.quotation-success-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

.quotation-success-modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.quotation-success-modal-header {
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.quotation-success-modal-body {
    padding: 1.5rem 2rem;
}

.quotation-success-modal-footer {
    padding: 1rem 2rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quotation-success-modal-footer .btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s;
}

.quotation-success-modal-footer .btn-primary {
    background-color: var(--md-sys-color-primary);
    color: white;
}

.quotation-success-modal-footer .btn-primary:hover {
    background-color: #5a4290;
}

.quotation-success-modal-footer .btn-secondary {
    background-color: #e0e0e0;
    color: var(--md-sys-color-on-surface);
}

.quotation-success-modal-footer .btn-secondary:hover {
    background-color: #d0d0d0;
}

@media (max-width: 768px) {
    .quotation-submit .container > div {
        grid-template-columns: 1fr !important;
    }
    
    .quotation-success-modal-content {
        width: 95%;
        margin: 10% auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quotation-submit-form');
    const submitBtn = document.getElementById('submit-quotation-btn');
    const errorsDiv = document.getElementById('form-errors');
    const successModal = document.getElementById('quotationSuccessModal');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide previous errors
            errorsDiv.style.display = 'none';
            errorsDiv.innerHTML = '';
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            
            // Get form data
            const formData = new FormData(form);
            
            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_csrf"]');
            if (csrfToken) {
                formData.append('_csrf', csrfToken.value);
            }
            
            // Send AJAX request
            fetch('<?= \yii\helpers\Url::to(["submit"]) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, it might be a redirect - throw error
                    throw new Error('La respuesta no es JSON. Posible redirección.');
                }
            })
            .then(data => {
                if (data.success) {
                    // Show success modal
                    successModal.style.display = 'block';
                    // Prevent any default behavior
                    return false;
                } else {
                    // Show errors
                    if (data.errors) {
                        let errorHtml = '<strong>Por favor, corrija los siguientes errores:</strong><ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">';
                        for (let field in data.errors) {
                            data.errors[field].forEach(error => {
                                errorHtml += '<li>' + error + '</li>';
                            });
                        }
                        errorHtml += '</ul>';
                        errorsDiv.innerHTML = errorHtml;
                        errorsDiv.style.display = 'block';
                    } else {
                        errorsDiv.innerHTML = '<strong>Error:</strong> ' + (data.message || 'Ocurrió un error al enviar la cotización.');
                        errorsDiv.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar Cotización';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorsDiv.innerHTML = '<strong>Error:</strong> Ocurrió un error al enviar la cotización. Por favor, intente nuevamente.';
                errorsDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Cotización';
            });
            
            return false; // Prevent form submission
        });
    }
    
    // Close modal when clicking outside (use event delegation)
    if (successModal) {
        successModal.addEventListener('click', function(event) {
            if (event.target === successModal) {
                successModal.style.display = 'none';
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && successModal && successModal.style.display === 'block') {
            successModal.style.display = 'none';
        }
    });
    
    // Prevent modal from closing when clicking inside modal content
    const modalContent = document.querySelector('.quotation-success-modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});
</script>

