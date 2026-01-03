<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'description')->textarea(['rows' => 6, 'class' => 'form-control']) ?>

<?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'form-control']) ?>

<?= $form->field($model, 'category_id')->dropDownList($categoryList, ['prompt' => 'Seleccione una categoría', 'class' => 'form-control']) ?>

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

<?= $form->field($model, 'video_url')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'https://www.youtube.com/watch?v=... o https://youtu.be/...']) ?>
<p class="help-block">Ingrese la URL completa del video de YouTube (ejemplo: https://www.youtube.com/watch?v=VIDEO_ID)</p>

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
            $relatedProducts = Product::find()->where(['id' => $relatedIds])->all();
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

<?= $form->field($model, 'status')->dropDownList([
    \app\models\Product::STATUS_ACTIVE => 'Activo',
    \app\models\Product::STATUS_INACTIVE => 'Inactivo',
], ['class' => 'form-control']) ?>

<div class="form-group">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<style>
.image-preview-container {
    margin-top: 1rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.image-preview-item {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 4px;
    overflow: hidden;
    border: 2px solid #ddd;
}

.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview-item .preview-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 2px 4px;
    font-size: 10px;
    text-align: center;
}

.existing-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.5rem;
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
    border-radius: 4px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 2px;
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
    padding: 0.5rem 0.75rem;
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
let searchTimeout = null;
const searchInput = document.getElementById('relatedProductSearch');
const searchResults = document.getElementById('relatedProductResults');
const selectedContainer = document.getElementById('selectedRelatedProducts');
const productId = <?= $model->isNewRecord ? 0 : $model->id ?>;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            fetch('/admin/product/search?q=' + encodeURIComponent(query) + '&exclude_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Error:', error);
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

function displaySearchResults(results) {
    if (results.length === 0) {
        searchResults.innerHTML = '<div style="padding: 0.5rem;">No se encontraron productos</div>';
        searchResults.style.display = 'block';
        return;
    }
    
    let html = '';
    results.forEach(function(product) {
        // Check if already selected
        const existing = selectedContainer.querySelector('[data-product-id="' + product.id + '"]');
        if (existing) {
            return;
        }
        
        html += '<div class="search-result-item" onclick="addRelatedProduct(' + product.id + ', \'' + escapeHtml(product.name) + '\')">';
        html += '<strong>' + escapeHtml(product.name) + '</strong>';
        html += '<span style="color: #666; margin-left: 0.5rem;">' + escapeHtml(product.price) + '</span>';
        html += '</div>';
    });
    
    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

function addRelatedProduct(id, name) {
    // Check if already added
    const existing = selectedContainer.querySelector('[data-product-id="' + id + '"]');
    if (existing) {
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'selected-product-item';
    div.setAttribute('data-product-id', id);
    
    div.innerHTML = '<span>' + name + '</span>' +
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
</script>

