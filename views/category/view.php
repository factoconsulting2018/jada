<?php

/** @var yii\web\View $this */
/** @var app\models\Category $category */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Category[] $categories */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\PriceHelper;

$this->title = $category->name;
?>
<div class="category-view">
    <div class="products-section">
        <h1 class="section-title"><?= Html::encode($category->name) ?></h1>
        
        <?php if ($category->description): ?>
            <p class="text-center" style="margin-bottom: 2rem; color: var(--md-sys-color-on-surface-variant);">
                <?= Html::encode($category->description) ?>
            </p>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <?php if (!empty($categories)): ?>
            <aside style="background: white; padding: 1.5rem; border-radius: 12px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; margin-bottom: 1rem;">Categorías</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.5rem;">
                        <a href="<?= Url::to(['/products']) ?>" style="text-decoration: none; color: var(--md-sys-color-on-surface);">Todas</a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="<?= Url::to(['/category/view', 'id' => $cat->id]) ?>" 
                           style="text-decoration: none; color: <?= $cat->id == $category->id ? 'var(--md-sys-color-primary)' : 'var(--md-sys-color-on-surface)' ?>; font-weight: <?= $cat->id == $category->id ? '500' : 'normal' ?>;">
                            <?= Html::encode($cat->name) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
            <?php endif; ?>

            <div>
                <?php
                $models = $dataProvider->getModels();
                if (empty($models)):
                ?>
                    <div class="text-center" style="padding: 3rem;">
                        <p>No hay productos en esta categoría.</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($models as $product): ?>
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
@media (max-width: 768px) {
    .category-view .products-section > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

