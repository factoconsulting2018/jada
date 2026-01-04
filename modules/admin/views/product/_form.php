<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ProductTechnicalSpec;
use app\models\ProductVideo;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<!-- Tabs Container -->
<div class="product-form-tabs-container">
    <div class="product-form-tabs">
        <button type="button" class="product-form-tab active" data-tab="general" onclick="switchProductFormTab('general')">
            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">info</span>
            Información General
        </button>
        <button type="button" class="product-form-tab" data-tab="images" onclick="switchProductFormTab('images')">
            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">image</span>
            Imágenes
        </button>
        <button type="button" class="product-form-tab" data-tab="videos" onclick="switchProductFormTab('videos')">
            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">play_circle</span>
            Vídeos
        </button>
        <button type="button" class="product-form-tab" data-tab="documents" onclick="switchProductFormTab('documents')">
            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">description</span>
            Documentos Técnicos
        </button>
        <button type="button" class="product-form-tab" data-tab="related" onclick="switchProductFormTab('related')">
            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">link</span>
            Productos Relacionados
        </button>
    </div>

    <!-- Tab 1: Información General -->
    <div class="product-form-tab-content active" id="product-form-tab-general">
        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6, 'class' => 'form-control']) ?>

            <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'form-control']) ?>

            <?= $form->field($model, 'category_id')->dropDownList($categoryList, ['prompt' => 'Seleccione una categoría', 'class' => 'form-control']) ?>

            <?= $form->field($model, 'status')->dropDownList([
                \app\models\Product::STATUS_ACTIVE => 'Activo',
                \app\models\Product::STATUS_INACTIVE => 'Inactivo',
            ], ['class' => 'form-control']) ?>
        </div>
    </div>

    <!-- Tab 2: Imágenes -->
    <div class="product-form-tab-content" id="product-form-tab-images">
        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
            <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'class' => 'form-control', 'id' => 'imageFiles', 'onchange' => 'previewImages(this)']) ?>
            <p class="help-block">Puede seleccionar hasta 20 imágenes. La primera será la imagen principal.</p>

            <div id="imagePreview" class="image-preview-container" style="display: none; margin-top: 1rem;">
                <label class="form-label">Vista Previa de Imágenes:</label>
                <div id="imagePreviewGrid" class="image-preview-grid"></div>
            </div>

            <?php if ($model->image): ?>
                <div class="form-group">
                    <label class="form-label">Imagen Principal Actual</label>
                    <div>
                        <?= Html::img($model->imageUrl, ['style' => 'max-width: 200px; margin-bottom: 1rem;']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php 
            $allImages = $model->getAllImages();
            if (count($allImages) > 1): ?>
                <div class="form-group">
                    <label class="form-label">Imágenes Adicionales</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                        <?php foreach (array_slice($allImages, 1) as $img): ?>
                            <?= Html::img(Yii::getAlias('@web') . $img, ['style' => 'width: 100px; height: 100px; object-fit: cover; border-radius: 4px;']) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 3: Vídeos -->
    <div class="product-form-tab-content" id="product-form-tab-videos">
        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
            <?php 
            $existingVideos = [];
            if (!$model->isNewRecord) {
                $existingVideos = ProductVideo::find()
                    ->where(['product_id' => $model->id])
                    ->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC])
                    ->all();
            }
            // Si hay un video_url antiguo pero no hay videos en la nueva tabla, migrarlo
            if (!$model->isNewRecord && $model->video_url && empty($existingVideos)) {
                $oldVideo = new ProductVideo();
                $oldVideo->product_id = $model->id;
                $oldVideo->video_url = $model->video_url;
                $oldVideo->name = null;
                $existingVideos = [$oldVideo];
            }
            ?>
            
            <!-- Vídeos existentes -->
            <div id="existingProductVideos" style="margin-bottom: 1.5rem;">
                <?php foreach ($existingVideos as $index => $video): ?>
                    <?php if (!$video->isNewRecord): ?>
                        <div class="product-video-item" data-video-id="<?= $video->id ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f5f5f5; border-radius: 8px; margin-bottom: 0.75rem;">
                            <input type="hidden" name="product_videos[<?= $video->id ?>][id]" value="<?= $video->id ?>">
                            <div style="flex: 1;">
                                <input type="text" 
                                       name="product_videos[<?= $video->id ?>][name]" 
                                       value="<?= Html::encode($video->name) ?>" 
                                       class="form-control" 
                                       placeholder="Nombre del vídeo"
                                       style="margin-bottom: 0.5rem;">
                                <input type="text" 
                                       name="product_videos[<?= $video->id ?>][video_url]" 
                                       value="<?= Html::encode($video->video_url) ?>" 
                                       class="form-control" 
                                       placeholder="URL del vídeo de YouTube"
                                       style="margin-bottom: 0.5rem;">
                                <a href="<?= Html::encode($video->video_url) ?>" 
                                   target="_blank" 
                                   style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--md-sys-color-primary); text-decoration: none; font-size: 0.875rem;">
                                    <span class="material-icons" style="font-size: 18px;">play_circle</span>
                                    Ver vídeo
                                </a>
                            </div>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="removeProductVideo(<?= $video->id ?>)"
                                    style="padding: 0.5rem 1rem; min-width: auto;">
                                <span class="material-icons" style="font-size: 18px;">delete</span>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Nuevos vídeos -->
            <div id="newProductVideos"></div>
            
            <button type="button" 
                    class="btn btn-secondary" 
                    onclick="addProductVideoField()"
                    style="margin-bottom: 1rem;">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add</span>
                Agregar Vídeo
            </button>
            
            <p class="help-block">Puede agregar múltiples vídeos de YouTube. Para cada vídeo, indique un nombre personalizado y la URL completa del vídeo (ejemplo: https://www.youtube.com/watch?v=VIDEO_ID). Si solo hay un vídeo, se mostrará directamente. Si hay múltiples, se mostrarán como botones.</p>
        </div>
    </div>

    <!-- Tab 4: Documentos Técnicos -->
    <div class="product-form-tab-content" id="product-form-tab-documents">
        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
            <?php 
            $existingSpecs = [];
            if (!$model->isNewRecord) {
                $existingSpecs = ProductTechnicalSpec::find()
                    ->where(['product_id' => $model->id])
                    ->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC])
                    ->all();
            }
            ?>
            
            <!-- Documentos existentes -->
            <div id="existingTechnicalSpecs" style="margin-bottom: 1.5rem;">
                <?php foreach ($existingSpecs as $index => $spec): ?>
                    <div class="technical-spec-item" data-spec-id="<?= $spec->id ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f5f5f5; border-radius: 8px; margin-bottom: 0.75rem;">
                        <input type="hidden" name="technical_specs[<?= $spec->id ?>][id]" value="<?= $spec->id ?>">
                        <div style="flex: 1;">
                            <input type="text" 
                                   name="technical_specs[<?= $spec->id ?>][name]" 
                                   value="<?= Html::encode($spec->name) ?>" 
                                   class="form-control" 
                                   placeholder="Nombre del documento"
                                   style="margin-bottom: 0.5rem;">
                            <a href="<?= Html::encode($spec->getFileUrl()) ?>" 
                               target="_blank" 
                               style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--md-sys-color-primary); text-decoration: none; font-size: 0.875rem;">
                                <span class="material-icons" style="font-size: 18px;">description</span>
                                <?= Html::encode($spec->getDisplayName()) ?>
                            </a>
                        </div>
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="removeTechnicalSpec(<?= $spec->id ?>)"
                                style="padding: 0.5rem 1rem; min-width: auto;">
                            <span class="material-icons" style="font-size: 18px;">delete</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Nuevos documentos -->
            <div id="newTechnicalSpecs"></div>
            
            <button type="button" 
                    class="btn btn-secondary" 
                    onclick="addTechnicalSpecField()"
                    style="margin-bottom: 1rem;">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add</span>
                Agregar Documento
            </button>
            
            <p class="help-block">Puede agregar múltiples documentos PDF. Para cada documento, indique un nombre personalizado. Máximo 10MB por archivo.</p>
        </div>
    </div>

    <!-- Tab 5: Productos Relacionados -->
    <div class="product-form-tab-content" id="product-form-tab-related">
        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
            <div class="form-group">
                <label class="control-label">Productos Relacionados</label>
                <div class="related-products-search">
                    <input type="text" id="relatedProductSearch" class="form-control" placeholder="Buscar productos para relacionar..." autocomplete="off">
                    <div id="relatedProductResults" class="related-products-results" style="display: none;"></div>
                </div>
                <div id="selectedRelatedProducts" class="selected-related-products" style="margin-top: 1rem;">
                    <?php 
                    $relatedIds = $model->isNewRecord ? [] : $model->getRelatedProductIds();
                    $relatedProducts = [];
                    if (!empty($relatedIds)) {
                        $relatedProducts = \app\models\Product::find()->where(['id' => $relatedIds])->all();
                    }
                    foreach ($relatedProducts as $related): ?>
                        <div class="selected-product-item" data-product-id="<?= $related->id ?>">
                            <span><?= Html::encode($related->name) ?></span>
                            <span class="remove-related-product" onclick="removeRelatedProduct(<?= $related->id ?>)">×</span>
                            <input type="hidden" name="related_products[]" value="<?= $related->id ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="help-block">Busque y seleccione productos relacionados que se mostrarán al cliente.</p>
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="form-group" style="margin-top: 2rem;">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<style>
.product-form-tabs-container {
    margin-top: 1rem;
}

.product-form-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 0;
    flex-wrap: wrap;
    gap: 0;
}

.product-form-tab {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--md-sys-color-on-surface-variant);
    transition: all 0.3s ease;
    position: relative;
    bottom: -2px;
    display: flex;
    align-items: center;
}

.product-form-tab:hover {
    color: var(--md-sys-color-primary);
    background-color: rgba(103, 80, 164, 0.05);
}

.product-form-tab.active {
    color: var(--md-sys-color-primary);
    border-bottom-color: var(--md-sys-color-primary);
    background-color: transparent;
}

.product-form-tab-content {
    display: none;
    margin-top: 0;
}

.product-form-tab-content.active {
    display: block;
}

.image-preview-container {
    margin-top: 1rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.image-preview-item {
    position: relative;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}

.preview-label {
    padding: 0.5rem;
    background: var(--md-sys-color-primary);
    color: white;
    text-align: center;
    font-size: 0.875rem;
    font-weight: 500;
}

.related-products-search {
    position: relative;
}

.related-products-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.search-result-item {
    padding: 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.search-result-item:hover {
    background-color: #f5f5f5;
}

.search-result-item:last-child {
    border-bottom: none;
}

.selected-related-products {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.selected-product-item {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 4px;
    color: #1976d2;
}

.remove-related-product {
    cursor: pointer;
    font-size: 1.5rem;
    line-height: 1;
    color: #d32f2f;
    font-weight: bold;
}

.remove-related-product:hover {
    color: #b71c1c;
}
</style>

<script>
// Product Form Tabs
function switchProductFormTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.product-form-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.product-form-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('product-form-tab-' + tabName);
    if (selectedContent) {
        selectedContent.classList.add('active');
    }
    
    // Add active class to selected tab
    const selectedTab = document.querySelector('.product-form-tab[data-tab="' + tabName + '"]');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}

function previewImages(input) {
    const previewContainer = document.getElementById('imagePreview');
    const previewGrid = document.getElementById('imagePreviewGrid');
    
    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'block';
        previewGrid.innerHTML = '';
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview ' + (index + 1);
                
                const label = document.createElement('div');
                label.className = 'preview-label';
                label.textContent = index === 0 ? 'Principal' : 'Imagen ' + (index + 1);
                
                div.appendChild(img);
                div.appendChild(label);
                previewGrid.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

// Related Products Search
let searchTimeout;
const searchInput = document.getElementById('relatedProductSearch');
const searchResults = document.getElementById('relatedProductResults');
const selectedContainer = document.getElementById('selectedRelatedProducts');
const currentProductId = <?= $model->isNewRecord ? 'null' : $model->id ?>;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            fetch('/admin/product/search?q=' + encodeURIComponent(query) + (currentProductId ? '&exclude_id=' + currentProductId : ''))
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(function(product) {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = '<strong>' + escapeHtml(product.name) + '</strong><br><small>' + escapeHtml(product.price) + '</small>';
                            div.onclick = function() {
                                addRelatedProduct(product.id, product.name);
                            };
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.style.display = 'none';
                });
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });
}

function addRelatedProduct(id, name) {
    // Check if already added
    if (selectedContainer.querySelector('[data-product-id="' + id + '"]')) {
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'selected-product-item';
    div.setAttribute('data-product-id', id);
    div.innerHTML = '<span>' + escapeHtml(name) + '</span>' +
                   '<span class="remove-related-product" onclick="removeRelatedProduct(' + id + ')">×</span>' +
                   '<input type="hidden" name="related_products[]" value="' + id + '">';
    
    selectedContainer.appendChild(div);
    searchInput.value = '';
    searchResults.style.display = 'none';
}

function removeRelatedProduct(id) {
    const item = selectedContainer.querySelector('[data-product-id="' + id + '"]');
    if (item) {
        item.remove();
    }
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

// Technical Specs Management
let technicalSpecCounter = 0;

function addTechnicalSpecField() {
    const container = document.getElementById('newTechnicalSpecs');
    const index = technicalSpecCounter++;
    
    const div = document.createElement('div');
    div.className = 'technical-spec-item new-spec';
    div.setAttribute('data-index', index);
    div.style.cssText = 'display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px; margin-bottom: 0.75rem;';
    
    div.innerHTML = `
        <div style="flex: 1;">
            <input type="text" 
                   name="technical_specs_new[${index}][name]" 
                   class="form-control" 
                   placeholder="Nombre del documento (ej: Ficha Técnica - Modelo XYZ)"
                   style="margin-bottom: 0.75rem;"
                   required>
            <input type="file" 
                   name="technical_specs_new[${index}][file]" 
                   accept="application/pdf"
                   class="form-control"
                   required>
        </div>
        <button type="button" 
                class="btn btn-danger" 
                onclick="this.closest('.technical-spec-item').remove()"
                style="padding: 0.5rem 1rem; min-width: auto;">
            <span class="material-icons" style="font-size: 18px;">delete</span>
        </button>
    `;
    
    container.appendChild(div);
}

function removeTechnicalSpec(id) {
    const item = document.querySelector(`.technical-spec-item[data-spec-id="${id}"]`);
    if (item) {
        // Mark as deleted
        item.style.display = 'none';
        const hiddenInput = item.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            // Keep the id input and add a delete input
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = hiddenInput.name.replace('[id]', '[delete]');
            deleteInput.value = '1';
            item.appendChild(deleteInput);
        }
    }
}

// Product Videos Management
let productVideoCounter = 0;

function addProductVideoField() {
    const container = document.getElementById('newProductVideos');
    const index = productVideoCounter++;
    
    const div = document.createElement('div');
    div.className = 'product-video-item new-video';
    div.setAttribute('data-index', index);
    div.style.cssText = 'display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px; margin-bottom: 0.75rem;';
    
    div.innerHTML = `
        <div style="flex: 1;">
            <input type="text" 
                   name="product_videos_new[${index}][name]" 
                   class="form-control" 
                   placeholder="Nombre del vídeo (ej: Video Tutorial - Instalación)"
                   style="margin-bottom: 0.75rem;"
                   required>
            <input type="text" 
                   name="product_videos_new[${index}][video_url]" 
                   class="form-control"
                   placeholder="URL del vídeo de YouTube (https://www.youtube.com/watch?v=...)"
                   required>
        </div>
        <button type="button" 
                class="btn btn-danger" 
                onclick="this.closest('.product-video-item').remove()"
                style="padding: 0.5rem 1rem; min-width: auto;">
            <span class="material-icons" style="font-size: 18px;">delete</span>
        </button>
    `;
    
    container.appendChild(div);
}

function removeProductVideo(id) {
    const item = document.querySelector(`.product-video-item[data-video-id="${id}"]`);
    if (item) {
        // Mark as deleted
        item.style.display = 'none';
        const hiddenInput = item.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            // Keep the id input and add a delete input
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = hiddenInput.name.replace('[id]', '[delete]');
            deleteInput.value = '1';
            item.appendChild(deleteInput);
        }
    }
}
</script>
