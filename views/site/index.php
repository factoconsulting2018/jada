<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Banner;
use app\models\Product;
use app\models\Category;
use app\models\ParallaxBackground;
use app\models\SponsorBanner;
use app\models\Configuration;
use app\helpers\PriceHelper;

$this->title = 'Inicio';

$showSectionBanner = Configuration::getValue('section_banner', '1') == '1';
$showSectionProducts = Configuration::getValue('section_products', '1') == '1';
$showSectionCategories = Configuration::getValue('section_categories', '1') == '1';
$showSectionSponsors = Configuration::getValue('section_sponsors', '1') == '1';

$banners = Banner::getActiveBanners();

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
    <?php if ($showSectionBanner && !empty($banners)): ?>
    <div class="hero-banner">
        <?php foreach ($banners as $index => $banner): ?>
        <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
            <?php 
            $videoEmbedUrl = $banner->getYouTubeEmbedUrl();
            if ($videoEmbedUrl): 
            ?>
                <div class="hero-video-container">
                    <iframe 
                        src="<?= Html::encode($videoEmbedUrl) ?>" 
                        frameborder="0" 
                        allow="autoplay; encrypted-media" 
                        allowfullscreen
                        class="hero-video-background">
                    </iframe>
                    <?php if ($banner->image): ?>
                        <div class="hero-video-fallback" style="background-image: url('<?= Html::encode($banner->imageUrl) ?>');"></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php if ($banner->link): ?>
                    <a href="<?= Html::encode($banner->link) ?>">
                        <img src="<?= Html::encode($banner->imageUrl) ?>" alt="<?= Html::encode($banner->title) ?>">
                    </a>
                <?php else: ?>
                    <img src="<?= Html::encode($banner->imageUrl) ?>" alt="<?= Html::encode($banner->title) ?>">
                <?php endif; ?>
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

    <?php 
    $blockTitle = Configuration::getValue('block_title', '');
    $blockContent = Configuration::getValue('block_content', '');
    if (!empty($blockTitle) || !empty($blockContent)): 
    ?>
    <div class="content-block" style="padding: 2rem 2rem; background-color: #f5f5f5; margin: 0;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <?php if (!empty($blockTitle)): ?>
                <h2 class="block-title" style="font-size: 2rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--md-sys-color-on-surface); text-align: center;">
                    <?= Html::encode($blockTitle) ?>
                </h2>
            <?php endif; ?>
            <?php if (!empty($blockContent)): ?>
                <div class="block-content" style="font-size: 1.125rem; line-height: 1.8; color: var(--md-sys-color-on-surface-variant); text-align: center; white-space: pre-wrap;">
                    <?= nl2br(Html::encode($blockContent)) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($showSectionProducts): ?>
    <section class="products-section parallax-section" data-section="products">
        <?php if (!empty($parallaxBackgrounds['products'])): ?>
            <?php 
            // Mostrar solo el primer fondo parallax activo (posición 1)
            $firstBg = reset($parallaxBackgrounds['products']);
            if ($firstBg): 
                $overlayColor = $firstBg->overlay_color ?: '#FFFFFF';
                $overlayOpacity = $firstBg->overlay_opacity ?: 0.3;
                // Convert hex to rgba
                $hex = str_replace('#', '', $overlayColor);
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $rgba = "rgba({$r}, {$g}, {$b}, {$overlayOpacity})";
            ?>
                <div class="parallax-background" data-image="<?= Html::encode($firstBg->imageUrl) ?>" style="background-image: url('<?= Html::encode($firstBg->imageUrl) ?>');">
                    <div class="parallax-overlay" style="background-color: <?= Html::encode($rgba) ?>;"></div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="container">
            <h2 class="products-section-title" style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem; background-color: #1976D2; color: white; text-align: center; width: 100%; padding: 1rem; border-radius: 8px;">
                Nuestros productos
            </h2>
            <div class="products-scroll-wrapper">
                <button class="products-scroll-btn products-scroll-left" id="productsScrollLeft" aria-label="Productos anteriores">
                    <span class="material-icons">chevron_left</span>
                </button>
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
                <button class="products-scroll-btn products-scroll-right" id="productsScrollRight" aria-label="Siguientes productos">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($showSectionCategories && !empty($categories)): ?>
        <section class="categories-section parallax-section" data-section="categories">
            <?php if (!empty($parallaxBackgrounds['categories'])): ?>
                <?php 
                // Mostrar solo el primer fondo parallax activo (posición 1)
                $firstCategoryBg = reset($parallaxBackgrounds['categories']);
                if ($firstCategoryBg): 
                    $overlayColorCat = $firstCategoryBg->overlay_color ?: '#FFFFFF';
                    $overlayOpacityCat = $firstCategoryBg->overlay_opacity ?: 0.3;
                    // Convert hex to rgba
                    $hexCat = str_replace('#', '', $overlayColorCat);
                    $rCat = hexdec(substr($hexCat, 0, 2));
                    $gCat = hexdec(substr($hexCat, 2, 2));
                    $bCat = hexdec(substr($hexCat, 4, 2));
                    $rgbaCat = "rgba({$rCat}, {$gCat}, {$bCat}, {$overlayOpacityCat})";
                ?>
                    <div class="parallax-background" data-image="<?= Html::encode($firstCategoryBg->imageUrl) ?>" style="background-image: url('<?= Html::encode($firstCategoryBg->imageUrl) ?>');">
                        <div class="parallax-overlay" style="background-color: <?= Html::encode($rgbaCat) ?>;"></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="container">
                <h2 style="font-size: 2rem; font-weight: 400; margin-bottom: 1.5rem; color: var(--md-sys-color-on-surface);">
                    Categorías
                </h2>
                <div class="category-grid" id="categoryTags">
                    <div class="category-card active" data-category-id="all">
                        <div class="category-card-image">
                            <span class="material-icons" style="font-size: 48px; color: var(--md-sys-color-primary);">apps</span>
                        </div>
                        <div class="category-card-content">
                            <h3 class="category-card-title">Todas</h3>
                        </div>
                    </div>
                    <?php foreach ($categories as $index => $category): ?>
                        <?php 
                        $colorIndex = $index % count($categoryColors);
                        $color = $categoryColors[$colorIndex];
                        ?>
                        <div class="category-card" data-category-id="<?= $category->id ?>" style="border-top: 4px solid <?= $color ?>;">
                            <div class="category-card-image">
                                <?php if ($category->image): ?>
                                    <img src="<?= Html::encode($category->imageUrl) ?>" alt="<?= Html::encode($category->name) ?>">
                                <?php else: ?>
                                    <span class="material-icons" style="font-size: 48px; color: <?= $color ?>;">category</span>
                                <?php endif; ?>
                            </div>
                            <div class="category-card-content">
                                <h3 class="category-card-title"><?= Html::encode($category->name) ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showSectionSponsors && !empty($sponsorBanners)): ?>
        <section class="sponsors-section">
            <div class="container">
                <h2 class="sponsors-title">Nuestras marcas</h2>
                <div class="sponsors-grid">
                    <?php foreach ($sponsorBanners as $sponsor): ?>
                        <?php if ($sponsor->link): ?>
                            <a href="<?= Html::encode($sponsor->link) ?>" target="_blank" rel="noopener noreferrer" class="sponsor-item">
                                <img src="<?= Html::encode($sponsor->imageUrl) ?>" alt="<?= Html::encode($sponsor->title ?: 'Patrocinador') ?>">
                            </a>
                        <?php else: ?>
                            <div class="sponsor-item">
                                <img src="<?= Html::encode($sponsor->imageUrl) ?>" alt="<?= Html::encode($sponsor->title ?: 'Patrocinador') ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
.parallax-section {
    position: relative;
    overflow: hidden;
}

.parallax-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    z-index: -1;
    pointer-events: none;
}

.parallax-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}

.products-section,
.categories-section {
    position: relative;
    padding: 2rem 0;
    width: 100%;
}


.products-section .container,
.categories-section .container {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.products-section > .container > h2,
.categories-section > .container > h2 {
    position: relative;
    z-index: 1;
}

.products-grid,
.category-grid {
    position: relative;
    z-index: 1;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.category-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    user-select: none;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.category-card.active {
    box-shadow: 0 4px 16px rgba(103, 80, 164, 0.3);
    border-color: var(--md-sys-color-primary);
    background-color: rgba(103, 80, 164, 0.05);
}

.category-card-image {
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9f9f9;
    overflow: hidden;
}

.category-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-card-content {
    padding: 0.75rem 1rem;
    text-align: center;
}

.category-card-title {
    margin: 0;
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--md-sys-color-on-surface);
}

.products-scroll-wrapper {
    position: relative;
    width: 100%;
    margin: 0 auto 3rem;
}

.products-grid {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 1rem 0 2rem;
    width: 100%;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(103, 80, 164, 0.3) transparent;
}

.products-scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
    transition: all 0.3s ease;
    padding: 0;
}

.products-scroll-btn:hover {
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    transform: translateY(-50%) scale(1.1);
}

.products-scroll-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.products-scroll-btn .material-icons {
    color: var(--md-sys-color-primary);
    font-size: 32px;
}

.products-scroll-left {
    left: -24px;
}

.products-scroll-right {
    right: -24px;
}

.products-scroll-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

.products-grid::-webkit-scrollbar {
    height: 8px;
}

.products-grid::-webkit-scrollbar-track {
    background: transparent;
}

.products-grid::-webkit-scrollbar-thumb {
    background-color: rgba(103, 80, 164, 0.3);
    border-radius: 4px;
}

.products-grid::-webkit-scrollbar-thumb:hover {
    background-color: rgba(103, 80, 164, 0.5);
}

.products-grid .product-card {
    opacity: 1;
    transition: opacity 0.3s ease, transform 0.3s ease;
    flex: 0 0 280px;
    min-width: 280px;
    max-width: 280px;
}

.products-grid .product-card.hidden {
    display: none;
}

@media (max-width: 768px) {
    .category-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }
    
    .category-card-image {
        height: 100px;
    }
    
    .category-card-image .material-icons {
        font-size: 40px !important;
    }
    
    .category-card-content {
        padding: 0.5rem 0.75rem;
    }
    
    .category-card-title {
        font-size: 0.875rem;
    }
    
    .products-scroll-wrapper {
        margin: 0 auto 3rem;
        padding: 0 1rem;
    }
    
    .products-grid {
        gap: 1rem;
        padding: 0.5rem 0 1.5rem;
    }
    
    .products-grid .product-card {
        flex: 0 0 240px;
        min-width: 240px;
        max-width: 240px;
    }
    
    .products-scroll-btn {
        width: 40px;
        height: 40px;
    }
    
    .products-scroll-btn .material-icons {
        font-size: 28px;
    }
    
    .products-scroll-left {
        left: -12px;
    }
    
    .products-scroll-right {
        right: -12px;
    }
}

@media (max-width: 480px) {
    .products-grid .product-card {
        flex: 0 0 200px;
        min-width: 200px;
        max-width: 200px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parallax effect
    const parallaxSections = document.querySelectorAll('.parallax-section');
    
    function updateParallax() {
        parallaxSections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const parallaxBg = section.querySelector('.parallax-background');
            
            if (parallaxBg && rect.top < window.innerHeight && rect.bottom > 0) {
                const scrolled = window.pageYOffset;
                const sectionTop = rect.top + scrolled;
                const rate = (scrolled - sectionTop) * 0.3;
                parallaxBg.style.transform = 'translateY(' + rate + 'px)';
            }
        });
    }
    
    window.addEventListener('scroll', updateParallax);
    updateParallax();
    
    // Products scroll buttons
    const productsGrid = document.getElementById('productsGrid');
    const productsScrollLeft = document.getElementById('productsScrollLeft');
    const productsScrollRight = document.getElementById('productsScrollRight');
    
    if (productsGrid && productsScrollLeft && productsScrollRight) {
        function updateScrollButtons() {
            const scrollLeft = productsGrid.scrollLeft;
            const scrollWidth = productsGrid.scrollWidth;
            const clientWidth = productsGrid.clientWidth;
            
            productsScrollLeft.disabled = scrollLeft === 0;
            productsScrollRight.disabled = scrollLeft >= scrollWidth - clientWidth - 10;
        }
        
        productsScrollLeft.addEventListener('click', function() {
            productsGrid.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });
        
        productsScrollRight.addEventListener('click', function() {
            productsGrid.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
        
        productsGrid.addEventListener('scroll', updateScrollButtons);
        updateScrollButtons();
    }
    
    const categoryTags = document.querySelectorAll('.category-card');
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
