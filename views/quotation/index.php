<?php

/** @var yii\web\View $this */
/** @var array $products */
/** @var float $total */

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\PriceHelper;

$this->title = 'Cotización';

// Get success data from session
$successData = Yii::$app->session->get('quotation_success_data');
?>
<div class="quotation-cart">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <h1 class="section-title">Mi Cotización</h1>

        <?php if (empty($products)): ?>
            <div class="empty-cart" style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <span class="material-icons" style="font-size: 64px; color: #ccc; margin-bottom: 1rem;">shopping_cart</span>
                <h2 style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1rem;">Tu carrito de cotización está vacío</h2>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 2rem;">Agrega productos usando el buscador para crear una cotización.</p>
                <a href="<?= Url::to(['/products']) ?>" class="btn btn-primary">Ver Productos</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; margin-top: 2rem;">
                <!-- Products List -->
                <div class="cart-products" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Productos</h2>
                    
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
                        <?php foreach ($products as $item): ?>
                            <div class="cart-item" data-product-id="<?= $item['product']->id ?>" style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e0e0e0; align-items: center;">
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
                    </div>
                </div>

                <!-- Summary -->
                <div class="cart-summary" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); height: fit-content;">
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

                    <a href="<?= Url::to(['/quotation/submit']) ?>" class="btn btn-primary" style="width: 100%; display: block; text-align: center; padding: 0.75rem; margin-top: 1.5rem;">
                        Continuar con Cotización
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($successData): ?>
<!-- Success Modal -->
<div id="successModal" class="success-modal" style="display: block;">
    <div class="success-modal-content">
        <div class="success-modal-header">
            <span class="material-icons" style="font-size: 48px; color: #4caf50; margin-bottom: 1rem;">check_circle</span>
            <h2 style="margin: 0; color: #4caf50;">¡Cotización Enviada con Éxito!</h2>
            <p style="margin: 0.5rem 0 0 0; color: var(--md-sys-color-on-surface-variant);">Cotización #<?= $successData['quotation_id'] ?></p>
        </div>
        <div class="success-modal-body">
            <p style="margin-bottom: 1.5rem;">Recibirá un correo electrónico con los detalles de su cotización.</p>
            
            <div style="margin-bottom: 1.5rem;">
                <h3 style="margin-top: 0; font-size: 1rem;">Datos de Contacto</h3>
                <p style="margin: 0.25rem 0;"><strong>Nombre:</strong> <?= Html::encode($successData['full_name']) ?></p>
                <p style="margin: 0.25rem 0;"><strong>Email:</strong> <?= Html::encode($successData['email']) ?></p>
                <p style="margin: 0.25rem 0;"><strong>WhatsApp:</strong> <?= Html::encode($successData['whatsapp']) ?></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h3 style="margin-top: 0; font-size: 1rem;">Resumen de Productos</h3>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0.5rem;">
                    <?php foreach ($successData['products'] as $product): ?>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem; border-bottom: 1px solid #f0f0f0;">
                            <span><?= Html::encode($product['product_name']) ?> (x<?= $product['quantity'] ?>)</span>
                            <span style="font-weight: 500;">₡<?= number_format($product['subtotal'], 2, '.', ',') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e0e0e0;">
                    <span style="font-weight: 500; font-size: 1.125rem;">Total:</span>
                    <span style="font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                        ₡<?= number_format($successData['total'], 2, '.', ',') ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="success-modal-footer">
            <button type="button" onclick="closeSuccessModal()" class="btn btn-primary" style="width: 100%;">
                Cerrar
            </button>
        </div>
    </div>
</div>

<style>
.success-modal {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

.success-modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
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

.success-modal-header {
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.success-modal-body {
    padding: 2rem;
    max-height: 60vh;
    overflow-y: auto;
}

.success-modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e0e0e0;
}

@media (max-width: 768px) {
    .success-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .success-modal-header,
    .success-modal-body,
    .success-modal-footer {
        padding: 1.5rem;
    }
}
</style>

<script>
function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('successModal');
    if (event.target == modal) {
        closeSuccessModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuccessModal();
    }
});
</script>

<?php 
// Clear success data from session after displaying
Yii::$app->session->remove('quotation_success_data');
endif; 
?>

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

@media (max-width: 768px) {
    .quotation-cart .container > div {
        grid-template-columns: 1fr !important;
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

function removeFromCart(productId) {
    if (!confirm('¿Desea eliminar este producto del carrito?')) {
        return;
    }
    
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
            location.reload();
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
</script>

