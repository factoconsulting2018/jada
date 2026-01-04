<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SponsorBanner;
use app\models\ParallaxBackground;

/** @var yii\web\View $this */
/** @var string $whatsappNumber */
/** @var string $siteTitle */
/** @var string $footerText */
/** @var string $dollarPrice */
/** @var string $showDollarPrice */
/** @var app\models\SponsorBanner[] $sponsorBanners */
/** @var app\models\ParallaxBackground[] $productParallaxBackgrounds */

$this->title = 'Configuración';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-index">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-error">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div style="margin-top: 2rem;">
            <h2 style="margin-bottom: 1rem;">Configuración General del Sitio</h2>
            
            <div class="form-group">
                <label for="site_title" class="form-label">Título del Sitio</label>
                <input type="text" 
                       id="site_title" 
                       name="site_title" 
                       class="form-control" 
                       value="<?= Html::encode($siteTitle) ?>" 
                       placeholder="Tienda Online"
                       maxlength="255">
                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                    Título que aparecerá en el encabezado del sitio web.
                </small>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label for="footer_text" class="form-label">Texto del Footer</label>
                <textarea id="footer_text" 
                          name="footer_text" 
                          class="form-control" 
                          rows="3"
                          placeholder="© <?= date('Y') ?> Tienda Online. Todos los derechos reservados."><?= Html::encode($footerText) ?></textarea>
                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                    Texto que aparecerá en el pie de página del sitio web. Puede incluir múltiples líneas.
                </small>
            </div>
        </div>

            <div style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1rem;">Configuración de Precios</h2>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 2rem;">
                    Configura el precio del dólar y si deseas mostrar el precio aproximado en dólares en los productos.
                </p>

                <div class="form-group">
                    <label for="dollar_price" class="form-label">Precio del Dólar (₡)</label>
                    <input type="text" 
                           id="dollar_price" 
                           name="dollar_price" 
                           class="form-control" 
                           value="<?= Html::encode($dollarPrice) ?>" 
                           placeholder="500.00"
                           step="0.01">
                    <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                        Precio del dólar en colones costarricenses. Se usará para calcular el precio aproximado en USD.
                    </small>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" 
                               id="show_dollar_price" 
                               name="show_dollar_price" 
                               value="1"
                               <?= $showDollarPrice == '1' ? 'checked' : '' ?>
                               style="margin-right: 0.5rem; width: auto;">
                        <span>Mostrar precio en dólares en los productos</span>
                    </label>
                    <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                        Si está activado, se mostrará el precio aproximado en dólares junto al precio en colones.
                    </small>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1rem;">Configuración de WhatsApp</h2>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 2rem;">
                    Configura el número de WhatsApp que se utilizará en el botón de contacto de los productos.
                    Ingresa el número sin el símbolo + ni espacios (ejemplo: 50612345678).
                </p>

                <div class="form-group">
                    <label for="whatsapp_number" class="form-label">Número de WhatsApp</label>
                    <input type="text" 
                           id="whatsapp_number" 
                           name="whatsapp_number" 
                           class="form-control" 
                           value="<?= Html::encode($whatsappNumber) ?>" 
                           placeholder="50612345678"
                           maxlength="20">
                    <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                        Solo números. Sin + ni espacios.
                    </small>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1rem;">Banners de Patrocinadores</h2>
                <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 2rem;">
                    Gestiona los 4 banners de patrocinadores que aparecen en la página de inicio, debajo de las categorías.
                </p>

                <div class="sponsor-tabs-container">
                    <div class="sponsor-tabs">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <button type="button" 
                                    class="sponsor-tab <?= $i === 1 ? 'active' : '' ?>" 
                                    data-tab="<?= $i ?>"
                                    onclick="switchSponsorTab(<?= $i ?>)">
                                Patrocinador <?= $i ?>
                            </button>
                        <?php endfor; ?>
                    </div>

                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php $banner = $sponsorBanners[$i]; ?>
                        <div class="sponsor-tab-content" id="sponsor-tab-<?= $i ?>" style="<?= $i !== 1 ? 'display: none;' : '' ?>">
                            <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                                <div class="form-group">
                                    <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 1rem;">
                                        <input type="checkbox" 
                                               name="sponsor_status_<?= $i ?>" 
                                               value="1"
                                               <?= $banner->status == SponsorBanner::STATUS_ACTIVE ? 'checked' : '' ?>
                                               style="margin-right: 0.5rem; width: auto;">
                                        <span>Activar este banner</span>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label for="sponsor_title_<?= $i ?>" class="form-label">Título</label>
                                    <input type="text" 
                                           id="sponsor_title_<?= $i ?>" 
                                           name="sponsor_title_<?= $i ?>" 
                                           class="form-control" 
                                           value="<?= Html::encode($banner->title) ?>" 
                                           placeholder="Título del patrocinador"
                                           maxlength="255">
                                </div>

                                <div class="form-group" style="margin-top: 1rem;">
                                    <label for="sponsor_link_<?= $i ?>" class="form-label">Enlace (URL)</label>
                                    <input type="url" 
                                           id="sponsor_link_<?= $i ?>" 
                                           name="sponsor_link_<?= $i ?>" 
                                           class="form-control" 
                                           value="<?= Html::encode($banner->link) ?>" 
                                           placeholder="https://ejemplo.com"
                                           maxlength="255">
                                </div>

                                <div class="form-group" style="margin-top: 1rem;">
                                    <label for="sponsor_image_<?= $i ?>" class="form-label">Imagen</label>
                                    <?php if ($banner->image): ?>
                                        <div style="margin-bottom: 0.5rem;">
                                            <img src="<?= Html::encode($banner->imageUrl) ?>" 
                                                 alt="<?= Html::encode($banner->title) ?>" 
                                                 style="max-width: 200px; max-height: 100px; object-fit: contain; border: 1px solid #e0e0e0; padding: 0.5rem; background: #f5f5f5; border-radius: 4px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" 
                                           id="sponsor_image_<?= $i ?>" 
                                           name="sponsor_image_<?= $i ?>" 
                                           accept="image/png,image/jpeg,image/jpg,image/webp"
                                           class="form-control">
                                    <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                                        Formatos: PNG, JPG, JPEG, WEBP. Tamaño máximo: 5MB.
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <style>
            .sponsor-tabs-container {
                margin-top: 1rem;
            }

            .sponsor-tabs {
                display: flex;
                border-bottom: 2px solid #e0e0e0;
                margin-bottom: 0;
            }

            .sponsor-tab {
                padding: 0.75rem 1.5rem;
                background: transparent;
                border: none;
                border-bottom: 2px solid transparent;
                cursor: pointer;
                font-size: 1rem;
                font-weight: 500;
                color: var(--md-sys-color-on-surface-variant);
                transition: all 0.3s ease;
                position: relative;
                bottom: -2px;
            }

            .sponsor-tab:hover {
                color: var(--md-sys-color-primary);
                background-color: rgba(103, 80, 164, 0.05);
            }

            .sponsor-tab.active {
                color: var(--md-sys-color-primary);
                border-bottom-color: var(--md-sys-color-primary);
                background-color: transparent;
            }

            .sponsor-tab-content {
                margin-top: 0;
            }
            </style>

            <script>
            function switchSponsorTab(tabNumber) {
                // Hide all sponsor tab contents
                for (let i = 1; i <= 4; i++) {
                    const content = document.getElementById('sponsor-tab-' + i);
                    if (content) {
                        content.style.display = 'none';
                    }
                    const tab = document.querySelector('.sponsor-tab[data-tab="' + i + '"]');
                    if (tab) {
                        tab.classList.remove('active');
                    }
                }
                
                // Show selected tab content
                document.getElementById('sponsor-tab-' + tabNumber).style.display = 'block';
                event.target.classList.add('active');
            }
            
            function switchParallaxTab(tabNumber) {
                // Hide all parallax tab contents
                for (let i = 1; i <= 4; i++) {
                    const content = document.getElementById('parallax-tab-' + i);
                    if (content) {
                        content.style.display = 'none';
                    }
                    const tab = document.querySelector('.sponsor-tab[data-tab="parallax-' + i + '"]');
                    if (tab) {
                        tab.classList.remove('active');
                    }
                }
                
                // Show selected tab content
                document.getElementById('parallax-tab-' + tabNumber).style.display = 'block';
                event.target.classList.add('active');
            }
            </script>

        <!-- Product Parallax Backgrounds Section -->
        <div class="config-section" style="margin-top: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 1rem; color: var(--md-sys-color-on-surface);">
                Fondos Parallax de Productos
            </h2>
            <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1.5rem;">
                Gestiona los fondos parallax que aparecen en la página de productos.
            </p>

            <div class="sponsor-tabs-container">
                <div class="sponsor-tabs">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <button type="button" 
                                class="sponsor-tab <?= $i === 1 ? 'active' : '' ?>" 
                                data-tab="parallax-<?= $i ?>"
                                onclick="switchParallaxTab(<?= $i ?>)">
                            Fondo <?= $i ?>
                        </button>
                    <?php endfor; ?>
                </div>

                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php $bg = $productParallaxBackgrounds[$i]; ?>
                    <div class="sponsor-tab-content" id="parallax-tab-<?= $i ?>" style="<?= $i !== 1 ? 'display: none;' : '' ?>">
                        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 1rem;">
                                    <input type="checkbox" 
                                           name="product_parallax_status_<?= $i ?>" 
                                           value="1"
                                           <?= $bg->status == ParallaxBackground::STATUS_ACTIVE ? 'checked' : '' ?>
                                           style="margin-right: 0.5rem; width: auto;">
                                    <span>Activar este fondo</span>
                                </label>
                            </div>

                            <div class="form-group" style="margin-top: 1rem;">
                                <label for="product_parallax_image_<?= $i ?>" class="form-label">Imagen de Fondo</label>
                                <?php if ($bg->image): ?>
                                    <div style="margin-bottom: 0.5rem;">
                                        <img src="<?= Html::encode($bg->imageUrl) ?>" 
                                             alt="Fondo parallax <?= $i ?>" 
                                             style="max-width: 300px; max-height: 200px; object-fit: cover; border: 1px solid #e0e0e0; padding: 0.5rem; background: #f5f5f5; border-radius: 4px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" 
                                       id="product_parallax_image_<?= $i ?>" 
                                       name="product_parallax_image_<?= $i ?>" 
                                       accept="image/png,image/jpeg,image/jpg,image/webp"
                                       class="form-control">
                                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                                    Formatos: PNG, JPG, JPEG, WEBP. Tamaño máximo: 10MB.
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <?= Html::submitButton('Guardar Configuración', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

