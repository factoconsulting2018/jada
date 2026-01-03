<?php

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Category[] $categories */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Productos';
?>
<div class="product-index">
    <div class="products-section">
        <h1 class="section-title">Catálogo de Productos</h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem; margin-bottom: 2rem;">
            <?php if (!empty($categories)): ?>
            <aside style="background: white; padding: 1.5rem; border-radius: 12px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; margin-bottom: 1rem;">Categorías</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.5rem;">
                        <a href="<?= Url::to(['/products']) ?>" style="text-decoration: none; color: var(--md-sys-color-on-surface);">Todas</a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="<?= Url::to(['/category/view', 'id' => $category->id]) ?>" style="text-decoration: none; color: var(--md-sys-color-on-surface);">
                            <?= Html::encode($category->name) ?>
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
                        <p>No se encontraron productos.</p>
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
                                <?php if ($product->category): ?>
                                    <p class="product-category"><?= Html::encode($product->category->name) ?></p>
                                <?php endif; ?>
                                <div class="product-price"><?= Html::encode($product->formattedPrice) ?></div>
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
    .product-index .products-section > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

