<?php

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Category[] $categories */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\PriceHelper;

$this->title = 'Productos';
?>
<div class="product-index">
    <div class="products-section parallax-section" data-section="products_page">
        <?php if (!empty($parallaxBackgrounds)): ?>
            <?php foreach ($parallaxBackgrounds as $bg): ?>
                <div class="parallax-background" data-image="<?= Html::encode($bg->imageUrl) ?>" style="background-image: url('<?= Html::encode($bg->imageUrl) ?>');"></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <h1 class="section-title" style="font-size: 2rem; font-weight: 500; margin-bottom: 2rem; text-align: center;">Catálogo de Productos</h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem; margin-bottom: 2rem;">
            <?php if (!empty($categories)): ?>
            <aside style="background: white; padding: 1.5rem; border-radius: 12px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; margin-bottom: 1rem;">Categorías</h3>
                <div class="category-accordion">
                    <div class="category-accordion-item">
                        <a href="<?= Url::to(['/products']) ?>" class="category-accordion-link" style="text-decoration: none; color: var(--md-sys-color-on-surface); font-weight: 500;">
                            Todas
                        </a>
                    </div>
                    <?php foreach ($categories as $cat): ?>
                    <?php 
                    $subcategories = \app\models\Category::getSubcategories($cat->id);
                    $hasSubcategories = !empty($subcategories);
                    ?>
                    <div class="category-accordion-item">
                        <button class="category-accordion-button <?= $hasSubcategories ? 'has-subcategories' : '' ?>" data-category-id="<?= $cat->id ?>">
                            <a href="<?= Url::to(['/category/view', 'id' => $cat->id]) ?>" 
                               style="text-decoration: none; color: var(--md-sys-color-on-surface); flex: 1; text-align: left;">
                                <?= Html::encode($cat->name) ?>
                            </a>
                            <?php if ($hasSubcategories): ?>
                                <span class="accordion-arrow material-icons">keyboard_arrow_down</span>
                            <?php endif; ?>
                        </button>
                        <?php if ($hasSubcategories): ?>
                        <div class="category-accordion-content" style="display: none;">
                            <?php foreach ($subcategories as $subcat): ?>
                            <a href="<?= Url::to(['/category/view', 'id' => $subcat->id]) ?>" 
                               class="category-subcategory-link"
                               style="text-decoration: none; color: var(--md-sys-color-on-surface-variant); display: block; padding: 0.5rem 1rem 0.5rem 2rem;">
                                <?= Html::encode($subcat->name) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </aside>
            <?php endif; ?>

            <div>
                <?php
                $models = $dataProvider->getModels();
                if (empty($models)):
                ?>
                    <div class="text-center" style="padding: 3rem;">
                        <p>No se encontraron productos.</p>
                    </div>
                <?php else: ?>
                         <div class="products-grid product-index-grid">
                             <?php foreach ($models as $product): ?>
                             <a href="<?= Url::to(['/product/view', 'id' => $product->id]) ?>" class="product-card">
                                 <?php if ($product->image): ?>
                                     <div class="product-image-container" style="width: 100%; aspect-ratio: 1 / 1; overflow: hidden; border-radius: 8px; background: #f5f5f5;">
                                         <img src="<?= Html::encode($product->imageUrl) ?>" alt="<?= Html::encode($product->name) ?>" class="product-image" style="width: 100%; height: 100%; object-fit: cover;">
                                     </div>
                                 <?php else: ?>
                                     <div class="product-image" style="width: 100%; aspect-ratio: 1 / 1; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                         <span class="material-icons" style="font-size: 64px; color: #ccc;">inventory_2</span>
                                     </div>
                                 <?php endif; ?>
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
                                        <div class="product-price" style="margin: 0; font-size: 1.125rem; font-weight: 500;"><?= Html::encode($product->formattedPrice) ?></div>
                                    </div>
                                    <?php if ($dollarPrice): ?>
                                        <div style="text-align: right;">
                                            <span style="font-size: 0.625rem; color: var(--md-sys-color-on-surface-variant); display: block; margin-bottom: 0.25rem;">Precio aprox en dólares</span>
                                            <div style="font-size: 0.875rem; color: var(--md-sys-color-on-surface-variant);"><?= Html::encode($dollarPrice) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 2rem;">
                        <?= LinkPager::widget([
                            'pagination' => $dataProvider->pagination,
                            'options' => ['class' => 'pagination'],
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.category-accordion {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.category-accordion-item {
    border-bottom: 1px solid #e0e0e0;
}

.category-accordion-item:last-child {
    border-bottom: none;
}

.category-accordion-link {
    display: block;
    padding: 0.75rem 1rem;
    transition: background-color 0.2s ease;
}

.category-accordion-link:hover {
    background-color: #f5f5f5;
}

.category-accordion-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: background-color 0.2s ease;
    font-size: inherit;
    font-family: inherit;
}

.category-accordion-button:hover {
    background-color: #f5f5f5;
}

.category-accordion-button.has-subcategories {
    cursor: pointer;
}

.accordion-arrow {
    transition: transform 0.3s ease;
    font-size: 1.25rem;
    color: var(--md-sys-color-on-surface-variant);
    margin-left: 0.5rem;
}

.category-accordion-item.active .accordion-arrow {
    transform: rotate(180deg);
}

.category-accordion-content {
    background-color: #fafafa;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.category-subcategory-link {
    transition: background-color 0.2s ease, color 0.2s ease;
}

.category-subcategory-link:hover {
    background-color: #f0f0f0;
    color: var(--md-sys-color-primary) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionButtons = document.querySelectorAll('.category-accordion-button.has-subcategories');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Prevent default link behavior if clicking on the button area (not the link)
            if (e.target === button || e.target.classList.contains('accordion-arrow')) {
                e.preventDefault();
            }
            
            const item = this.closest('.category-accordion-item');
            const content = item.querySelector('.category-accordion-content');
            
            // Toggle active class
            item.classList.toggle('active');
            
            // Toggle content visibility
            if (item.classList.contains('active')) {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
            }
        });
    });
});
</script>

     <style>
     .product-index-grid {
         grid-template-columns: repeat(3, 1fr) !important;
     }

     .product-index-grid .product-image-container {
         width: 100%;
         aspect-ratio: 1 / 1;
         overflow: hidden;
         background: #f5f5f5;
     }

     .product-index-grid .product-image-container img {
         width: 100%;
         height: 100%;
         object-fit: cover;
     }

     @media (max-width: 1024px) {
         .product-index-grid {
             grid-template-columns: repeat(2, 1fr) !important;
         }
     }

     @media (max-width: 768px) {
         .product-index .products-section > div {
             grid-template-columns: 1fr !important;
         }
         .product-index-grid {
             grid-template-columns: 1fr !important;
         }
     }
     </style>

     <style>
     .parallax-section {
         position: relative;
         overflow: hidden;
         width: 100%;
     }

     .product-index .products-section {
         position: relative !important;
         width: 100% !important;
         max-width: none !important;
         margin: 0 !important;
         padding: 2rem 0;
     }

     .product-index .parallax-background {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         width: 100%;
         height: 100%;
         min-height: 100%;
         background-size: cover;
         background-position: center center;
         background-repeat: no-repeat;
         z-index: -1;
         opacity: 0.3;
         pointer-events: none;
     }

     .product-index .products-section > h1,
     .product-index .products-section > div {
         max-width: 1200px;
         margin-left: auto;
         margin-right: auto;
         padding-left: 2rem;
         padding-right: 2rem;
         position: relative;
         z-index: 1;
     }
     </style>

     <script>
     document.addEventListener('DOMContentLoaded', function() {
         // Parallax effect for products page
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
     });
     </script>

