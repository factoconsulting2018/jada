<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Banner;
use app\models\Product;
use app\models\Category;
use app\helpers\PriceHelper;

$this->title = 'Inicio';

$banners = Banner::getActiveBanners();
$featuredProducts = Product::find()
    ->where(['status' => Product::STATUS_ACTIVE])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(20)
    ->all();

$categories = Category::find()
    ->where(['status' => Category::STATUS_ACTIVE])
    ->limit(12)
    ->all();

// Generate colors for categories
$categoryColors = [
    '#6750A4', '#625B71', '#7D5260', '#006A6B', '#006C4C',
    '#87695B', '#8C4A3E', '#6E5B52', '#5C5D5E', '#6B4E71',
    '#7B5D5D', '#5D6B71'
];
?>

<div class="site-index">
    <?php if (!empty($banners)): ?>
    <div class="hero-banner">
        <?php foreach ($banners as $index => $banner): ?>
        <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
            <?php if ($banner->link): ?>
                <a href="<?= Html::encode($banner->link) ?>">
                    <img src="<?= Html::encode($banner->imageUrl) ?>" alt="<?= Html::encode($banner->title) ?>">
                </a>
            <?php else: ?>
                <img src="<?= Html::encode($banner->imageUrl) ?>" alt="<?= Html::encode($banner->title) ?>">
            <?php endif; ?>
            <div class="hero-content">
                <h2 class="hero-title"><?= Html::encode($banner->title) ?></h2>
                <?php if ($banner->subtitle): ?>
                    <p class="hero-subtitle"><?= Html::encode($banner->subtitle) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="hero-search-container">
            <div class="hero-search-wrapper">
                <input type="text" id="hero-search-input" class="hero-search-input" placeholder="Buscar productos..." autocomplete="off">
                <span class="material-icons hero-search-icon">search</span>
                <div id="hero-search-suggestions" class="hero-search-suggestions"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        <?php if (!empty($categories)): ?>
            <section class="categories-section" style="margin-bottom: 3rem;">
                <h2 style="font-size: 2rem; font-weight: 400; margin-bottom: 1.5rem; color: var(--md-sys-color-on-surface);">
                    Categorías
                </h2>
                <div class="category-tags" id="categoryTags">
                    <span class="category-tag active" data-category-id="all" style="background-color: var(--md-sys-color-primary); color: white;">
                        Todas
                    </span>
                    <?php foreach ($categories as $index => $category): ?>
                        <?php 
                        $colorIndex = $index % count($categoryColors);
                        $color = $categoryColors[$colorIndex];
                        ?>
                        <span class="category-tag" data-category-id="<?= $category->id ?>" style="background-color: <?= $color ?>; color: white;">
                            <?= Html::encode($category->name) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="products-section">
            <h2 style="font-size: 2rem; font-weight: 400; margin-bottom: 1.5rem; color: var(--md-sys-color-on-surface);">
                Productos
            </h2>
            <div class="products-grid" id="productsGrid">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): ?>
                        <a href="<?= Url::to(['/product/view', 'id' => $product->id]) ?>" class="product-card" data-category-id="<?= $product->category_id ?>">
                            <img src="<?= Html::encode($product->imageUrl) ?>" alt="<?= Html::encode($product->name) ?>" class="product-image">
                                <div class="product-info">
                                    <h3 class="product-name"><?= Html::encode($product->name) ?></h3>
                                    <?php if ($product->category): ?>
                                        <p class="product-category"><?= Html::encode($product->category->name) ?></p>
                                    <?php endif; ?>
                                    <?php 
                                    $dollarPrice = PriceHelper::formatDollars($product->price);
                                    ?>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 0.5rem;">
                                        <div>
                                            <span style="font-size: 0.75rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio</span>
                                            <p class="product-price" style="margin: 0; font-size: 1.125rem; font-weight: 500;"><?= Html::encode($product->formattedPrice) ?></p>
                                        </div>
                                        <?php if ($dollarPrice): ?>
                                            <div style="text-align: right;">
                                                <span style="font-size: 0.625rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio aprox en dólares</span>
                                                <p style="margin: 0; font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);"><?= Html::encode($dollarPrice) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: var(--md-sys-color-on-surface-variant); padding: 3rem;">
                        No hay productos disponibles.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
.category-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.category-tag {
    display: inline-block;
    padding: 0.625rem 1.25rem;
    border-radius: 24px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    user-select: none;
}

.category-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.category-tag.active {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transform: scale(1.05);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin-bottom: 3rem;
}

.products-grid .product-card {
    opacity: 1;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.products-grid .product-card.hidden {
    display: none;
}

@media (max-width: 1024px) {
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .category-tags {
        gap: 0.5rem;
    }
    
    .category-tag {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryTags = document.querySelectorAll('.category-tag');
    const productsGrid = document.getElementById('productsGrid');
    let currentCategoryId = 'all';
    
    categoryTags.forEach(tag => {
        tag.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            
            // Update active state
            categoryTags.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            filterProducts(categoryId);
            currentCategoryId = categoryId;
        });
    });
    
    function filterProducts(categoryId) {
        // Show loading state
        productsGrid.style.opacity = '0.5';
        productsGrid.style.pointerEvents = 'none';
        
        // Make AJAX request
        fetch('/site/filter-products?category_id=' + encodeURIComponent(categoryId))
            .then(response => response.json())
            .then(data => {
                updateProductsGrid(data.products);
                productsGrid.style.opacity = '1';
                productsGrid.style.pointerEvents = 'auto';
            })
            .catch(error => {
                console.error('Error:', error);
                productsGrid.style.opacity = '1';
                productsGrid.style.pointerEvents = 'auto';
                // Fallback to client-side filtering
                filterProductsClientSide(categoryId);
            });
    }
    
    function updateProductsGrid(products) {
        if (products.length === 0) {
            productsGrid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; color: var(--md-sys-color-on-surface-variant); padding: 3rem;">No hay productos disponibles en esta categoría.</p>';
            return;
        }
        
        let html = '';
        products.forEach(product => {
            html += `
                <a href="${escapeHtml(product.url)}" class="product-card" data-category-id="${product.category || ''}">
                    <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}" class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">${escapeHtml(product.name)}</h3>
                        ${product.category ? `<p class="product-category">${escapeHtml(product.category)}</p>` : ''}
                        <p class="product-price">${escapeHtml(product.price)}</p>
                    </div>
                </a>
            `;
        });
        
        productsGrid.innerHTML = html;
    }
    
    function filterProductsClientSide(categoryId) {
        const productCards = productsGrid.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const cardCategoryId = card.getAttribute('data-category-id');
            if (categoryId === 'all' || cardCategoryId === categoryId) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });
        
        if (visibleCount === 0) {
            productsGrid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; color: var(--md-sys-color-on-surface-variant); padding: 3rem;">No hay productos disponibles en esta categoría.</p>';
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
});
</script>
