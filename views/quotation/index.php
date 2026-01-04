<?php

/** @var yii\web\View $this */
/** @var array $products */
/** @var float $total */
/** @var app\models\Quotation $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\helpers\PriceHelper;

$this->title = 'Cotización';
?>
<div class="quotation-cart">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <h1 class="section-title">Mi Cotización</h1>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; margin-top: 2rem;">
            <!-- Products List -->
            <div class="cart-products" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="margin: 0;">Productos</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <button type="button" id="compare-btn" onclick="toggleCompareMode()" style="padding: 0.75rem 1.5rem; background: var(--md-sys-color-primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: background-color 0.3s;">
                            <span class="material-icons">compare_arrows</span>
                            Comparar
                        </button>
                        <button type="button" id="show-comparison-list-btn" onclick="showComparisonList()" style="padding: 0.75rem 1.5rem; background: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; display: none; align-items: center; gap: 0.5rem;">
                            <span class="material-icons">view_list</span>
                            <span id="comparison-btn-text">Lista de Comparación</span>
                            <span id="comparison-count" style="background: rgba(255,255,255,0.3); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.875rem; margin-left: 0.25rem;">0</span>
                        </button>
                    </div>
                </div>
                
                <div id="product-search-container" style="margin-bottom: 1.5rem;">
                    <div style="position: relative;">
                        <input type="text" 
                               id="product-search-input" 
                               class="form-control" 
                               placeholder="Buscar productos para agregar..." 
                               autocomplete="off"
                               style="padding-right: 3rem;">
                        <span class="material-icons" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #666; pointer-events: none;">search</span>
                        <div id="product-search-results" class="product-search-results" style="display: none;"></div>
                    </div>
                </div>

                <div id="cart-items">
                    <?php if (empty($products)): ?>
                        <div class="empty-cart-message" style="text-align: center; padding: 2rem; color: var(--md-sys-color-on-surface-variant);">
                            <span class="material-icons" style="font-size: 48px; color: #ccc; margin-bottom: 1rem; display: block;">shopping_cart</span>
                            <p style="margin: 0;">Tu carrito de cotización está vacío. Agrega productos usando el buscador.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $item): ?>
                            <div class="cart-item" data-product-id="<?= $item['product']->id ?>" style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e0e0e0; align-items: center;">
                                <div class="compare-checkbox-container" style="display: none; flex-shrink: 0;">
                                    <input type="checkbox" 
                                           class="compare-checkbox" 
                                           id="compare-<?= $item['product']->id ?>" 
                                           data-product-id="<?= $item['product']->id ?>"
                                           data-product-name="<?= Html::encode($item['product']->name) ?>"
                                           data-product-price="<?= Html::encode($item['product']->formattedPrice) ?>"
                                           data-product-image="<?= Html::encode($item['product']->imageUrl) ?>"
                                           data-product-url="<?= Url::to(['/product/view', 'id' => $item['product']->id]) ?>"
                                           data-product-code="<?= Html::encode($item['product']->code ?: '') ?>"
                                           data-product-category="<?= Html::encode($item['product']->category ? $item['product']->category->name : '') ?>"
                                           data-product-brand="<?= Html::encode($item['product']->brand ? $item['product']->brand->name : '') ?>"
                                           data-product-description="<?= Html::encode($item['product']->description ?: '') ?>"
                                           onchange="updateComparisonList()"
                                           style="width: 20px; height: 20px; cursor: pointer;">
                                </div>
                                <div style="flex-shrink: 0;">
                                    <?php if ($item['product']->image): ?>
                                        <img src="<?= Html::encode($item['product']->imageUrl) ?>" 
                                             alt="<?= Html::encode($item['product']->name) ?>" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 80px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <span class="material-icons" style="font-size: 32px; color: #ccc;">inventory_2</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1;">
                                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 500;">
                                        <a href="<?= Url::to(['/product/view', 'id' => $item['product']->id]) ?>" style="text-decoration: none; color: var(--md-sys-color-on-surface);">
                                            <?= Html::encode($item['product']->name) ?>
                                        </a>
                                    </h3>
                                    <p style="margin: 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.875rem;">
                                        <?= Html::encode($item['product']->formattedPrice) ?>
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button type="button" 
                                            class="quantity-btn" 
                                            onclick="updateQuantity(<?= $item['product']->id ?>, -1)"
                                            style="width: 32px; height: 32px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <span class="material-icons" style="font-size: 18px;">remove</span>
                                    </button>
                                    <input type="number" 
                                           id="quantity-<?= $item['product']->id ?>" 
                                           value="<?= $item['quantity'] ?>" 
                                           min="1"
                                           onchange="updateQuantity(<?= $item['product']->id ?>, 0, this.value)"
                                           style="width: 60px; text-align: center; padding: 0.25rem; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" 
                                            class="quantity-btn" 
                                            onclick="updateQuantity(<?= $item['product']->id ?>, 1)"
                                            style="width: 32px; height: 32px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <span class="material-icons" style="font-size: 18px;">add</span>
                                    </button>
                                </div>
                                <div style="text-align: right; min-width: 100px;">
                                    <p style="margin: 0; font-weight: 500; font-size: 1rem;" id="subtotal-<?= $item['product']->id ?>">
                                        <?= Html::encode('₡' . number_format($item['subtotal'], 2, '.', ',')) ?>
                                    </p>
                                </div>
                                <button type="button" 
                                        onclick="removeFromCart(<?= $item['product']->id ?>)"
                                        style="background: none; border: none; cursor: pointer; color: #d32f2f; padding: 0.5rem;"
                                        title="Eliminar">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Comparison Info Modal -->
            <div id="comparison-info-modal" class="comparison-info-modal">
                <div class="comparison-info-modal-content">
                    <div style="text-align: center; padding: 2rem;">
                        <span class="material-icons" style="font-size: 64px; color: var(--md-sys-color-primary); margin-bottom: 1rem; display: block;">compare_arrows</span>
                        <h2 style="margin: 0 0 1rem 0; color: var(--md-sys-color-on-surface);">Modo Comparación Activado</h2>
                        <p style="margin: 0; color: var(--md-sys-color-on-surface-variant); font-size: 1rem; line-height: 1.5;">
                            Selecciona los productos que deseas comparar marcando las casillas de verificación que aparecen junto a cada producto.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Remove Product Confirmation Modal -->
            <div id="remove-confirm-modal" class="remove-confirm-modal">
                <div class="remove-confirm-modal-content">
                    <div style="text-align: center; padding: 2rem;">
                        <span class="material-icons" style="font-size: 64px; color: #d32f2f; margin-bottom: 1rem; display: block;">warning</span>
                        <h2 style="margin: 0 0 1rem 0; color: var(--md-sys-color-on-surface);">¿Eliminar producto?</h2>
                        <p style="margin: 0; color: var(--md-sys-color-on-surface-variant); font-size: 1rem; line-height: 1.5; margin-bottom: 2rem;">
                            ¿Desea eliminar este producto del carrito?
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <button type="button" id="remove-confirm-cancel" onclick="closeRemoveConfirmModal()" style="padding: 0.75rem 2rem; background: #e0e0e0; color: var(--md-sys-color-on-surface); border: none; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 1rem; transition: background-color 0.3s;">
                                Cancelar
                            </button>
                            <button type="button" id="remove-confirm-ok" onclick="confirmRemoveProduct()" style="padding: 0.75rem 2rem; background: #d32f2f; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 1rem; transition: background-color 0.3s;">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remove Product Success Modal -->
            <div id="remove-success-modal" class="remove-success-modal">
                <div class="remove-success-modal-content">
                    <div style="text-align: center; padding: 2rem;">
                        <span class="material-icons" style="font-size: 64px; color: #4CAF50; margin-bottom: 1rem; display: block;">check_circle</span>
                        <h2 style="margin: 0 0 1rem 0; color: var(--md-sys-color-on-surface);">¡Producto eliminado!</h2>
                        <p style="margin: 0; color: var(--md-sys-color-on-surface-variant); font-size: 1rem; line-height: 1.5;">
                            El producto ha sido eliminado del carrito con éxito.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Comparison Modal -->
            <div id="comparison-modal" class="comparison-modal">
                <div class="comparison-modal-content">
                    <div class="comparison-modal-header">
                        <h2>Lista de Comparación</h2>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="comparison-modal-header-actions">
                                <span class="material-icons comparison-modal-header-icon" onclick="printComparisonList()" title="Imprimir">print</span>
                                <span class="material-icons comparison-modal-header-icon" onclick="generateComparisonPDF()" title="Generar PDF">picture_as_pdf</span>
                            </div>
                            <span class="comparison-modal-close" onclick="closeComparisonModal()">&times;</span>
                        </div>
                    </div>
                    <div class="comparison-modal-body">
                        <div id="comparison-table-container"></div>
                    </div>
                </div>
            </div>

                <!-- Summary -->
                <div class="cart-summary" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); height: fit-content;">
                    <!-- Form -->
                    <div class="quotation-form-container" style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e0e0e0;">
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

                        <?php ActiveForm::end(); ?>
                    </div>

                    <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Resumen</h2>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: var(--md-sys-color-on-surface-variant);">Subtotal:</span>
                            <span id="cart-subtotal" style="font-weight: 500;">
                                <?= Html::encode('₡' . number_format($total, 2, '.', ',')) ?>
                            </span>
                        </div>
                        <?php 
                        $dollarTotal = PriceHelper::formatDollars($total);
                        if ($dollarTotal): 
                        ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: var(--md-sys-color-on-surface-variant); font-size: 0.875rem;">Aprox. en dólares:</span>
                            <span style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);">
                                <?= Html::encode($dollarTotal) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <div style="border-top: 1px solid #e0e0e0; margin-top: 1rem; padding-top: 1rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-weight: 500; font-size: 1.125rem;">Total:</span>
                                <span id="cart-total" style="font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                                    <?= Html::encode('₡' . number_format($total, 2, '.', ',')) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" form="quotation-submit-form" class="btn btn-primary" id="submit-quotation-btn" style="width: 100%; display: block; text-align: center; padding: 0.75rem; margin-top: 1.5rem;" <?= empty($products) ? 'disabled' : '' ?>>
                        Enviar Cotización
                    </button>
                </div>
            </div>
    </div>
</div>

<style>
.product-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 0.5rem;
}

.product-search-result-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.product-search-result-item:hover {
    background-color: #f5f5f5;
}

.product-search-result-item:last-child {
    border-bottom: none;
}

.product-search-result-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.product-search-result-item-info {
    flex: 1;
}

.product-search-result-item-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.product-search-result-item-price {
    color: var(--md-sys-color-on-surface-variant);
    font-size: 0.875rem;
}

.comparison-item {
    transition: background-color 0.2s ease;
}

.comparison-item:hover {
    background-color: #f0f0f0 !important;
}

/* Comparison Info Modal */
.comparison-info-modal {
    display: none;
    position: fixed;
    z-index: 2100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
    align-items: center;
    justify-content: center;
}

.comparison-info-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
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

/* Remove Confirm Modal */
.remove-confirm-modal {
    display: none;
    position: fixed;
    z-index: 2100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
    align-items: center;
    justify-content: center;
}

.remove-confirm-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}

.remove-confirm-modal-content button:hover {
    opacity: 0.9;
}

#remove-confirm-cancel:hover {
    background: #d0d0d0 !important;
}

#remove-confirm-ok:hover {
    background: #b71c1c !important;
}

/* Remove Success Modal */
.remove-success-modal {
    display: none;
    position: fixed;
    z-index: 2100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
    align-items: center;
    justify-content: center;
}

.remove-success-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}

.compare-checkbox-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.compare-checkbox {
    accent-color: var(--md-sys-color-primary);
}

#comparison-list-container {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Comparison Modal */
.comparison-modal {
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

.comparison-modal-content {
    background-color: #fefefe;
    margin: 2% auto;
    padding: 0;
    border-radius: 12px;
    width: 95%;
    max-width: 1400px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.comparison-modal-header {
    padding: 1.5rem 2rem;
    background-color: var(--md-sys-color-primary);
    color: white;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comparison-modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.comparison-modal-header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.comparison-modal-header-icon {
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.comparison-modal-header-icon:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.comparison-modal-close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.comparison-modal-close:hover {
    opacity: 0.7;
}

@media print {
    .comparison-modal-header-actions {
        display: none !important;
    }
    
    .comparison-modal-close {
        display: none !important;
    }
    
    .comparison-modal {
        position: static !important;
        display: block !important;
        background: transparent !important;
    }
    
    .comparison-modal-content {
        margin: 0 !important;
        box-shadow: none !important;
        max-height: none !important;
    }
    
    .comparison-modal-header {
        border-radius: 0 !important;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}

.comparison-modal-body {
    padding: 2rem;
    overflow-x: auto;
    flex: 1;
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.comparison-table th,
.comparison-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: top;
}

.comparison-table th {
    background-color: #f5f5f5;
    font-weight: 500;
    color: var(--md-sys-color-on-surface);
    position: sticky;
    left: 0;
    z-index: 10;
}

.comparison-table tr:last-child td {
    border-bottom: none;
}

.comparison-table .product-cell {
    min-width: 200px;
    max-width: 250px;
}

.comparison-product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.comparison-product-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.comparison-product-name a {
    color: var(--md-sys-color-primary);
    text-decoration: none;
}

.comparison-product-name a:hover {
    text-decoration: underline;
}

.comparison-product-code {
    font-size: 0.875rem;
    color: var(--md-sys-color-on-surface-variant);
    margin-top: 0.25rem;
}

.comparison-product-price {
    font-weight: 500;
    font-size: 1.125rem;
    color: var(--md-sys-color-primary);
}

@media (max-width: 768px) {
    .quotation-cart .container > div {
        grid-template-columns: 1fr !important;
    }
    
    .comparison-modal-content {
        width: 100%;
        margin: 0;
        border-radius: 0;
        max-height: 100vh;
    }
    
    .comparison-modal-header {
        border-radius: 0;
    }
    
    .comparison-modal-body {
        padding: 1rem;
    }
    
    .cart-products > div:first-child {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .cart-products > div:first-child > div {
        width: 100%;
    }
    
    #compare-btn,
    #show-comparison-list-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('product-search-input');
    const searchResults = document.getElementById('product-search-results');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(function() {
                fetch('/quotation/search?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }, 300);
        });
        
        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
});

function displaySearchResults(results) {
    const searchResults = document.getElementById('product-search-results');
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div style="padding: 1rem; text-align: center; color: #666;">No se encontraron productos</div>';
        searchResults.style.display = 'block';
        return;
    }
    
    let html = '';
    results.forEach(function(product) {
        html += '<div class="product-search-result-item" onclick="addToCart(' + product.id + ')">';
        html += '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '">';
        html += '<div class="product-search-result-item-info">';
        html += '<div class="product-search-result-item-name">' + escapeHtml(product.name) + '</div>';
        html += '<div class="product-search-result-item-price">' + escapeHtml(product.price) + '</div>';
        html += '</div>';
        html += '</div>';
    });
    
    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

function addToCart(productId) {
    fetch('/quotation/add-to-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al agregar producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar producto al carrito');
    });
}

let pendingRemoveProductId = null;

function removeFromCart(productId) {
    pendingRemoveProductId = productId;
    showRemoveConfirmModal();
}

function showRemoveConfirmModal() {
    const confirmModal = document.getElementById('remove-confirm-modal');
    if (confirmModal) {
        confirmModal.style.display = 'flex';
    }
}

function closeRemoveConfirmModal() {
    const confirmModal = document.getElementById('remove-confirm-modal');
    if (confirmModal) {
        confirmModal.style.display = 'none';
    }
    pendingRemoveProductId = null;
}

function confirmRemoveProduct() {
    if (!pendingRemoveProductId) {
        return;
    }
    
    const productId = pendingRemoveProductId;
    closeRemoveConfirmModal();
    
    fetch('/quotation/remove-from-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            const successModal = document.getElementById('remove-success-modal');
            if (successModal) {
                successModal.style.display = 'flex';
                
                // Auto close after 2 seconds and reload
                setTimeout(() => {
                    successModal.style.display = 'none';
                    location.reload();
                }, 2000);
            } else {
                // Fallback: reload immediately if modal not found
                location.reload();
            }
        } else {
            alert(data.message || 'Error al eliminar producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar producto del carrito');
    });
}

function updateQuantity(productId, change, newValue) {
    const quantityInput = document.getElementById('quantity-' + productId);
    let quantity = parseInt(quantityInput.value) || 1;
    
    if (newValue !== undefined) {
        quantity = parseInt(newValue) || 1;
    } else {
        quantity += change;
    }
    
    if (quantity < 1) {
        quantity = 1;
    }
    
    fetch('/quotation/update-quantity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            quantityInput.value = quantity;
            // Update subtotal and total
            const product = document.querySelector('[data-product-id="' + productId + '"]');
            const priceText = product.querySelector('p').textContent;
            const price = parseFloat(priceText.replace(/[₡,]/g, ''));
            const subtotal = price * quantity;
            
            document.getElementById('subtotal-' + productId).textContent = '₡' + data.subtotal;
            document.getElementById('cart-subtotal').textContent = '₡' + data.total;
            document.getElementById('cart-total').textContent = '₡' + data.total;
        } else {
            alert(data.message || 'Error al actualizar cantidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar cantidad');
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Comparison functionality
let compareModeActive = false;
let comparisonProducts = [];

function toggleCompareMode() {
    compareModeActive = !compareModeActive;
    const checkboxes = document.querySelectorAll('.compare-checkbox-container');
    const compareBtn = document.getElementById('compare-btn');
    const showComparisonListBtn = document.getElementById('show-comparison-list-btn');
    
    if (compareModeActive) {
        // Show checkboxes
        checkboxes.forEach(container => {
            container.style.display = 'block';
        });
        compareBtn.style.background = '#4caf50';
        compareBtn.innerHTML = '<span class="material-icons">check</span> Comparando...';
        
        // Show info modal
        const infoModal = document.getElementById('comparison-info-modal');
        if (infoModal) {
            infoModal.style.display = 'flex';
            
            // Auto close after 5 seconds
            setTimeout(() => {
                infoModal.style.display = 'none';
            }, 5000);
        }
    } else {
        // Hide checkboxes
        checkboxes.forEach(container => {
            container.style.display = 'none';
        });
        // Uncheck all checkboxes
        document.querySelectorAll('.compare-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        compareBtn.style.background = 'var(--md-sys-color-primary)';
        compareBtn.innerHTML = '<span class="material-icons">compare_arrows</span> Comparar';
        showComparisonListBtn.style.display = 'none';
        comparisonProducts = [];
    }
    updateComparisonCount();
}

function updateComparisonList() {
    const checkedBoxes = document.querySelectorAll('.compare-checkbox:checked');
    comparisonProducts = [];
    
    checkedBoxes.forEach(checkbox => {
        comparisonProducts.push({
            id: checkbox.getAttribute('data-product-id'),
            name: checkbox.getAttribute('data-product-name'),
            price: checkbox.getAttribute('data-product-price'),
            image: checkbox.getAttribute('data-product-image'),
            url: checkbox.getAttribute('data-product-url'),
            code: checkbox.getAttribute('data-product-code') || '',
            category: checkbox.getAttribute('data-product-category') || '',
            brand: checkbox.getAttribute('data-product-brand') || '',
            description: checkbox.getAttribute('data-product-description') || ''
        });
    });
    
    updateComparisonCount();
}

function updateComparisonCount() {
    const count = comparisonProducts.length;
    const showComparisonListBtn = document.getElementById('show-comparison-list-btn');
    const comparisonCount = document.getElementById('comparison-count');
    const comparisonBtnText = document.getElementById('comparison-btn-text');
    
    if (count > 0) {
        showComparisonListBtn.style.display = 'flex';
        comparisonCount.textContent = count;
        if (comparisonBtnText) {
            comparisonBtnText.textContent = 'Ver productos comparados';
        }
    } else {
        showComparisonListBtn.style.display = 'none';
        if (comparisonBtnText) {
            comparisonBtnText.textContent = 'Lista de Comparación';
        }
    }
}

function showComparisonList() {
    if (comparisonProducts.length === 0) {
        alert('No hay productos seleccionados para comparar.');
        return;
    }
    
    const modal = document.getElementById('comparison-modal');
    const tableContainer = document.getElementById('comparison-table-container');
    
    // Build comparison table
    let tableHtml = '<table class="comparison-table">';
    
    // Table header
    tableHtml += '<thead><tr>';
    tableHtml += '<th>Característica</th>';
    comparisonProducts.forEach(product => {
        tableHtml += '<th class="product-cell">';
        tableHtml += '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '" class="comparison-product-image">';
        tableHtml += '<div class="comparison-product-name"><a href="' + escapeHtml(product.url) + '" target="_blank">' + escapeHtml(product.name) + '</a></div>';
        if (product.code) {
            tableHtml += '<div class="comparison-product-code">Código: ' + escapeHtml(product.code) + '</div>';
        }
        tableHtml += '</th>';
    });
    tableHtml += '</tr></thead>';
    
    // Table body
    tableHtml += '<tbody>';
    
    // Price row
    tableHtml += '<tr>';
    tableHtml += '<th>Precio</th>';
    comparisonProducts.forEach(product => {
        tableHtml += '<td><div class="comparison-product-price">' + escapeHtml(product.price) + '</div></td>';
    });
    tableHtml += '</tr>';
    
    // Category row
    if (comparisonProducts.some(p => p.category)) {
        tableHtml += '<tr>';
        tableHtml += '<th>Categoría</th>';
        comparisonProducts.forEach(product => {
            tableHtml += '<td>' + (product.category ? escapeHtml(product.category) : '-') + '</td>';
        });
        tableHtml += '</tr>';
    }
    
    // Brand row
    if (comparisonProducts.some(p => p.brand)) {
        tableHtml += '<tr>';
        tableHtml += '<th>Marca</th>';
        comparisonProducts.forEach(product => {
            tableHtml += '<td>' + (product.brand ? escapeHtml(product.brand) : '-') + '</td>';
        });
        tableHtml += '</tr>';
    }
    
    // Description row
    if (comparisonProducts.some(p => p.description)) {
        tableHtml += '<tr>';
        tableHtml += '<th>Descripción</th>';
        comparisonProducts.forEach(product => {
            tableHtml += '<td style="max-width: 300px; word-wrap: break-word;">' + (product.description ? escapeHtml(product.description) : '-') + '</td>';
        });
        tableHtml += '</tr>';
    }
    
    tableHtml += '</tbody></table>';
    
    tableContainer.innerHTML = tableHtml;
    modal.style.display = 'block';
}

function closeComparisonModal() {
    const modal = document.getElementById('comparison-modal');
    modal.style.display = 'none';
}

function printComparisonList() {
    if (comparisonProducts.length === 0) {
        alert('No hay productos seleccionados para comparar.');
        return;
    }
    
    // Create a new window with formatted content for printing
    const printWindow = window.open('', '_blank');
    
    let printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Lista de Comparación</title>
            <style>
                @page {
                    size: letter;
                    margin: 2cm;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .product-page {
                    page-break-after: always;
                    page-break-inside: avoid;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    padding: 20px;
                }
                .product-page:last-child {
                    page-break-after: auto;
                }
                .product-header {
                    border-bottom: 2px solid #673AB7;
                    padding-bottom: 15px;
                    margin-bottom: 20px;
                }
                .product-title {
                    font-size: 24px;
                    font-weight: bold;
                    color: #673AB7;
                    margin: 0 0 10px 0;
                }
                .product-image-container {
                    text-align: center;
                    margin: 20px 0;
                }
                .product-image {
                    max-width: 300px;
                    max-height: 300px;
                    object-fit: contain;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                }
                .product-info-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .product-info-table th {
                    background-color: #f5f5f5;
                    padding: 12px;
                    text-align: left;
                    font-weight: 500;
                    width: 200px;
                    border: 1px solid #e0e0e0;
                }
                .product-info-table td {
                    padding: 12px;
                    border: 1px solid #e0e0e0;
                }
                .product-description {
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #f9f9f9;
                    border-left: 4px solid #673AB7;
                    line-height: 1.6;
                }
                .product-code {
                    font-size: 14px;
                    color: #666;
                    margin-top: 5px;
                }
                .product-price {
                    font-size: 20px;
                    font-weight: bold;
                    color: #673AB7;
                }
                @media print {
                    .product-page {
                        page-break-after: always;
                    }
                    .product-page:last-child {
                        page-break-after: auto;
                    }
                }
            </style>
        </head>
        <body>
    `;
    
    // Generate one page per product
    comparisonProducts.forEach((product, index) => {
        printContent += `
            <div class="product-page">
                <div class="product-header">
                    <h1 class="product-title">${escapeHtml(product.name)}</h1>
                    ${product.code ? `<div class="product-code">Código: ${escapeHtml(product.code)}</div>` : ''}
                </div>
                
                <div class="product-image-container">
                    <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}" class="product-image" onerror="this.style.display='none';">
                </div>
                
                <table class="product-info-table">
                    <tr>
                        <th>Precio</th>
                        <td><span class="product-price">${escapeHtml(product.price)}</span></td>
                    </tr>
                    ${product.category ? `
                    <tr>
                        <th>Categoría</th>
                        <td>${escapeHtml(product.category)}</td>
                    </tr>
                    ` : ''}
                    ${product.brand ? `
                    <tr>
                        <th>Marca</th>
                        <td>${escapeHtml(product.brand)}</td>
                    </tr>
                    ` : ''}
                </table>
                
                ${product.description ? `
                <div class="product-description">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #673AB7;">Descripción</h3>
                    <div>${escapeHtml(product.description).replace(/\\n/g, '<br>')}</div>
                </div>
                ` : ''}
            </div>
        `;
    });
    
    printContent += `
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for content to load, then trigger print
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

function generateComparisonPDF() {
    if (comparisonProducts.length === 0) {
        alert('No hay productos seleccionados para comparar.');
        return;
    }
    
    // Create a new window with the comparison table for PDF generation
    const printWindow = window.open('', '_blank');
    const tableContainer = document.getElementById('comparison-table-container');
    const tableHTML = tableContainer.innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Lista de Comparación</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #e0e0e0;
                    vertical-align: top;
                }
                th {
                    background-color: #f5f5f5;
                    font-weight: 500;
                }
                .comparison-product-image {
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                    border-radius: 8px;
                    margin-bottom: 8px;
                }
                .comparison-product-name {
                    font-weight: 500;
                    margin-bottom: 4px;
                }
                .comparison-product-name a {
                    color: #333;
                    text-decoration: none;
                }
                .comparison-product-code {
                    font-size: 0.875rem;
                    color: #666;
                    margin-top: 4px;
                }
                .comparison-product-price {
                    font-weight: 500;
                    font-size: 1.125rem;
                    color: #673AB7;
                }
                @media print {
                    @page {
                        size: letter landscape;
                        margin: 1cm;
                    }
                }
            </style>
        </head>
        <body>
            <h1 style="text-align: center; margin-bottom: 20px;">Lista de Comparación</h1>
            ${tableHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for content to load, then trigger print
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

// Close comparison modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('comparison-modal');
    if (modal && event.target === modal) {
        closeComparisonModal();
    }
    
    // Close remove confirm modal when clicking outside
    const removeConfirmModal = document.getElementById('remove-confirm-modal');
    if (removeConfirmModal && event.target === removeConfirmModal) {
        closeRemoveConfirmModal();
    }
});

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quotation-submit-form');
    const submitBtn = document.getElementById('submit-quotation-btn');
    const errorsDiv = document.getElementById('form-errors');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide previous errors
            if (errorsDiv) {
                errorsDiv.style.display = 'none';
                errorsDiv.innerHTML = '';
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch('<?= Url::to(['submit']) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    throw new Error('La respuesta no es JSON.');
                }
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Su cotización fue enviada con éxito.');
                    // Redirect to products page
                    window.location.href = '<?= Url::to(['/products']) ?>';
                } else {
                    // Show errors
                    if (errorsDiv) {
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
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar Cotización';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (errorsDiv) {
                    errorsDiv.innerHTML = '<strong>Error:</strong> Ocurrió un error al enviar la cotización. Por favor, intente nuevamente.';
                    errorsDiv.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Cotización';
            });
            
            return false;
        });
    }
});
</script>

