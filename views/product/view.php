<?php

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var app\models\Product[] $relatedProducts */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Configuration;
use app\helpers\PriceHelper;

$this->title = $model->name;

$whatsappNumber = Configuration::getValue('whatsapp_number', Yii::$app->params['whatsapp']['number'] ?? '1234567890');
$productUrl = Url::to(['/product/view', 'id' => $model->id], true);
$message = (Yii::$app->params['whatsapp']['defaultMessage'] ?? 'Hola, me interesa el siguiente producto:') . ' ' . $model->name . ' - ' . $productUrl;
$whatsappUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);

$allImages = $model->getAllImages();
$mainImage = !empty($allImages) ? $allImages[0] : ($model->imageUrl ?? '');
?>
<div class="product-detail">
    <div class="product-detail-content">
        <div class="product-gallery">
            <?php if (!empty($allImages)): ?>
                <div class="product-main-image-container">
                    <img src="<?= Html::encode(Yii::getAlias('@web') . $allImages[0]) ?>" alt="<?= Html::encode($model->name) ?>" class="product-main-image" id="main-image">
                    <?php if (count($allImages) > 1): ?>
                        <button class="image-nav-btn image-nav-prev" onclick="navigateImage(-1)" aria-label="Imagen anterior">
                            <span class="material-icons">chevron_left</span>
                        </button>
                        <button class="image-nav-btn image-nav-next" onclick="navigateImage(1)" aria-label="Imagen siguiente">
                            <span class="material-icons">chevron_right</span>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="product-thumbnails">
                    <?php if (count($allImages) > 1): ?>
                        <?php foreach ($allImages as $index => $image): ?>
                        <img src="<?= Html::encode(Yii::getAlias('@web') . $image) ?>" 
                             alt="<?= Html::encode($model->name) ?>" 
                             class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                             onclick="changeMainImageByIndex(<?= $index ?>);">
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($model->video_url && $model->getYouTubeVideoId()): ?>
                        <div class="product-video-thumbnail" onclick="openVideoModal('<?= Html::encode($model->getYouTubeEmbedUrl()) ?>')">
                            <img src="https://img.youtube.com/vi/<?= Html::encode($model->getYouTubeVideoId()) ?>/mqdefault.jpg" alt="Video" class="video-thumbnail-img">
                            <div class="video-play-overlay">
                                <span class="material-icons" style="font-size: 48px; color: white;">play_circle</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="product-main-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 400px;">
                    <span class="material-icons" style="font-size: 128px; color: #ccc;">inventory_2</span>
                </div>
                <?php if ($model->video_url && $model->getYouTubeVideoId()): ?>
                    <div class="product-thumbnails">
                        <div class="product-video-thumbnail" onclick="openVideoModal('<?= Html::encode($model->getYouTubeEmbedUrl()) ?>')">
                            <img src="https://img.youtube.com/vi/<?= Html::encode($model->getYouTubeVideoId()) ?>/mqdefault.jpg" alt="Video" class="video-thumbnail-img">
                            <div class="video-play-overlay">
                                <span class="material-icons" style="font-size: 48px; color: white;">play_circle</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Video Modal -->
        <?php if ($model->video_url && $model->getYouTubeEmbedUrl()): ?>
        <div id="videoModal" class="video-modal" style="display: none;" onclick="closeVideoModal()">
            <div class="video-modal-content" onclick="event.stopPropagation()">
                <span class="video-modal-close" onclick="closeVideoModal()">&times;</span>
                <iframe id="videoFrame" width="100%" height="500" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 12px;"></iframe>
            </div>
        </div>
        <?php endif; ?>

        <div class="product-info-detail">
            <h1><?= Html::encode($model->name) ?></h1>
            <?php if ($model->category): ?>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1rem;">
                    Categoría: <a href="<?= Url::to(['/category/view', 'id' => $model->category->id]) ?>" style="color: var(--md-sys-color-primary); text-decoration: none;">
                        <?= Html::encode($model->category->name) ?>
                    </a>
                </p>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 1rem; margin-bottom: 1rem;">
                <div>
                    <span style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio</span>
                    <div class="product-price-detail" style="margin: 0; font-size: 1.75rem; font-weight: 500;"><?= Html::encode($model->formattedPrice) ?></div>
                </div>
                <?php 
                $dollarPrice = PriceHelper::formatDollars($model->price);
                if ($dollarPrice): 
                ?>
                    <div style="text-align: right;">
                        <span style="font-size: 0.75rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio aprox en dólares</span>
                        <div style="font-size: 1.25rem; color: var(--md-sys-color-on-surface-variant);"><?= Html::encode($dollarPrice) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($model->description): ?>
                <div class="product-description">
                    <?= nl2br(Html::encode($model->description)) ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem;">
                <a href="<?= Html::encode($whatsappUrl) ?>" target="_blank" class="whatsapp-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
                <button type="button" class="quote-button" onclick="addProductToQuotation(<?= $model->id ?>)">
                    <span class="material-icons" style="margin-right: 8px; vertical-align: middle;">request_quote</span>
                    Agregar a Cotización
                </button>
            </div>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
    <div class="products-section">
        <h2 class="section-title">Tal vez te interese</h2>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $product): ?>
            <a href="<?= Url::to(['/product/view', 'id' => $product->id]) ?>" class="product-card">
                <?php if ($product->image): ?>
                    <img src="<?= Html::encode($product->imageUrl) ?>" alt="<?= Html::encode($product->name) ?>" class="product-image">
                <?php else: ?>
                    <div class="product-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 64px; color: #ccc;">inventory_2</span>
                    </div>
                <?php endif; ?>
                <div class="product-info">
                    <h3 class="product-name"><?= Html::encode($product->name) ?></h3>
                    <?php 
                    $dollarPriceRelated = PriceHelper::formatDollars($product->price);
                    ?>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 0.5rem;">
                        <div>
                            <span style="font-size: 0.75rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio</span>
                            <div class="product-price" style="margin: 0; font-size: 1.125rem; font-weight: 500;"><?= Html::encode($product->formattedPrice) ?></div>
                        </div>
                        <?php if ($dollarPriceRelated): ?>
                            <div style="text-align: right;">
                                <span style="font-size: 0.625rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio aprox en dólares</span>
                                <div style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);"><?= Html::encode($dollarPriceRelated) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de Cotización -->
<div id="quoteModal" class="quote-modal" style="display: none;">
    <div class="quote-modal-content">
        <div class="quote-modal-header">
            <h2>Solicitar Cotización</h2>
            <span class="quote-modal-close" onclick="document.getElementById('quoteModal').style.display='none'">&times;</span>
        </div>
        <form id="quoteForm" onsubmit="submitQuote(event, <?= $model->id ?>)">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
            
            <div class="form-group">
                <label for="id_type">Tipo de Identificación *</label>
                <select id="id_type" name="Quotation[id_type]" required>
                    <option value="">Seleccione...</option>
                    <option value="fisico">Físico</option>
                    <option value="juridico">Jurídico</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_number">Cédula *</label>
                <input type="text" id="id_number" name="Quotation[id_number]" required>
            </div>

            <div class="form-group">
                <label for="full_name">Nombre Completo *</label>
                <input type="text" id="full_name" name="Quotation[full_name]" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="Quotation[email]" required>
            </div>

            <div class="form-group">
                <label for="whatsapp">WhatsApp *</label>
                <input type="text" id="whatsapp" name="Quotation[whatsapp]" required>
            </div>

            <div id="quoteFormMessage" style="margin: 1rem 0; padding: 1rem; border-radius: 4px; display: none;"></div>

            <div class="form-actions">
                <button type="button" onclick="document.getElementById('quoteModal').style.display='none'" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<style>
.quote-button {
    background-color: var(--md-sys-color-primary);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 24px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: background-color 0.3s;
    margin-top: 1rem;
}

.quote-button:hover {
    background-color: #5a4290;
}

/* Modal Styles */
.quote-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.quote-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.quote-modal-header {
    padding: 1.5rem 2rem;
    background-color: var(--md-sys-color-primary);
    color: white;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.quote-modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.quote-modal-close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.quote-modal-close:hover {
    opacity: 0.7;
}

#quoteForm {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--md-sys-color-on-surface);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--md-sys-color-primary);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
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

.product-video-thumbnail {
    position: relative;
    width: 100%;
    height: 100px;
    cursor: pointer;
    border-radius: 8px;
    overflow: hidden;
    border: 3px solid transparent;
    transition: all 0.3s ease;
    background: #000;
}

.product-video-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border-color: var(--md-sys-color-primary);
}

.video-thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.4);
    transition: background 0.3s ease;
}

.product-video-thumbnail:hover .video-play-overlay {
    background: rgba(0, 0, 0, 0.6);
}

/* Video Modal */
.video-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
}

.video-modal-content {
    position: relative;
    background-color: transparent;
    margin: 5% auto;
    padding: 0;
    width: 90%;
    max-width: 900px;
}

.video-modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 2001;
}

.video-modal-close:hover {
    opacity: 0.7;
}

@media (max-width: 768px) {
    .product-detail-content {
        grid-template-columns: 1fr !important;
    }
    
    .image-nav-btn {
        width: 32px;
        height: 32px;
    }
    
    .image-nav-btn .material-icons {
        font-size: 20px;
    }
    
    .image-nav-prev {
        left: 5px;
    }
    
    .image-nav-next {
        right: 5px;
    }
    
    .product-thumbnails {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
    }
    
    .product-thumbnail,
    .product-video-thumbnail {
        height: 80px;
    }
    
    .video-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .video-modal-content iframe {
        height: 300px;
    }
    
    .quote-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    #quoteForm {
        padding: 1.5rem;
    }
}
</style>

<script>
var currentImageIndex = 0;
var allProductImages = <?= json_encode(array_map(function($img) use ($model) { return Yii::getAlias('@web') . $img; }, $allImages)) ?>;

function changeMainImageByIndex(index) {
    if (index < 0 || index >= allProductImages.length) return;
    
    currentImageIndex = index;
    const imageUrl = allProductImages[index];
    document.getElementById('main-image').src = imageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.product-thumbnail').forEach((thumb, thumbIndex) => {
        thumb.classList.remove('active');
        if (thumbIndex === index) {
            thumb.classList.add('active');
        }
    });
}

function navigateImage(direction) {
    if (allProductImages.length <= 1) return;
    
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = allProductImages.length - 1;
    } else if (currentImageIndex >= allProductImages.length) {
        currentImageIndex = 0;
    }
    
    const newImageUrl = allProductImages[currentImageIndex];
    document.getElementById('main-image').src = newImageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.product-thumbnail').forEach((thumb, index) => {
        thumb.classList.remove('active');
        if (index === currentImageIndex) {
            thumb.classList.add('active');
        }
    });
}

function openVideoModal(videoUrl) {
    const modal = document.getElementById('videoModal');
    const frame = document.getElementById('videoFrame');
    if (modal && frame) {
        frame.src = videoUrl + '?autoplay=1';
        modal.style.display = 'block';
    }
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const frame = document.getElementById('videoFrame');
    if (modal && frame) {
        modal.style.display = 'none';
        frame.src = '';
    }
}

// Close video modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVideoModal();
    }
});

function addProductToQuotation(productId) {
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
            // Show success message
            alert('Producto agregado al carrito de cotización exitosamente.');
        } else {
            alert(data.message || 'Error al agregar producto al carrito.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar producto al carrito. Por favor, intente nuevamente.');
    });
}

function submitQuote(event, productId) {
    event.preventDefault();
    
    // Add product to cart and redirect to quotation page
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
            // Redirect to quotation page
            window.location.href = '/quotation';
        } else {
            alert(data.message || 'Error al agregar producto al carrito. Por favor, intente nuevamente.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar producto al carrito. Por favor, intente nuevamente.');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('quoteModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

