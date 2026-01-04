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

        <!-- Main Tabs Container -->
        <div class="config-form-tabs-container" style="margin-top: 2rem;">
            <div class="config-form-tabs">
                <button type="button" class="config-form-tab active" data-tab="general" onclick="switchConfigTab('general')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">settings</span>
                    General
                </button>
                <button type="button" class="config-form-tab" data-tab="prices" onclick="switchConfigTab('prices')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">attach_money</span>
                    Precios
                </button>
                <button type="button" class="config-form-tab" data-tab="whatsapp" onclick="switchConfigTab('whatsapp')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">chat</span>
                    WhatsApp
                </button>
                <button type="button" class="config-form-tab" data-tab="sponsors" onclick="switchConfigTab('sponsors')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">business</span>
                    Banners Patrocinadores
                </button>
                <button type="button" class="config-form-tab" data-tab="parallax" onclick="switchConfigTab('parallax')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">wallpaper</span>
                    Fondos Parallax
                </button>
            </div>

            <!-- Tab 1: General -->
            <div class="config-form-tab-content active" id="config-form-tab-general">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
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
            </div>

            <!-- Tab 2: Precios -->
            <div class="config-form-tab-content" id="config-form-tab-prices">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
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
            </div>

            <!-- Tab 3: WhatsApp -->
            <div class="config-form-tab-content" id="config-form-tab-whatsapp">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
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
                            Configura el número de WhatsApp que se utilizará en el botón de contacto de los productos. Ingresa el número sin el símbolo + ni espacios (ejemplo: 50612345678).
                        </small>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Banners Patrocinadores -->
            <div class="config-form-tab-content" id="config-form-tab-sponsors">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                    <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1.5rem;">
                        Gestiona los 4 banners de patrocinadores que aparecen en la página de inicio, debajo de las categorías.
                    </p>

                    <div class="sponsor-tabs-container">
                        <div class="sponsor-tabs">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <button type="button" 
                                        class="sponsor-tab sponsor-tab-<?= $i ?> <?= $i === 1 ? 'active' : '' ?>" 
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
                                            Formatos: PNG, JPG, JPEG, WEBP. Tamaño máximo: 5MB.<br>
                                            <strong>Tamaño recomendado:</strong> 250px × 100px (proporción 2.5:1)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Fondos Parallax -->
            <div class="config-form-tab-content" id="config-form-tab-parallax">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
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
            </div>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <?= Html::submitButton('Guardar Configuración', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
.config-form-tabs-container {
    margin-top: 1rem;
}

.config-form-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 0;
    flex-wrap: wrap;
    gap: 0;
}

.config-form-tab {
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

.config-form-tab:hover {
    color: var(--md-sys-color-primary);
    background-color: rgba(103, 80, 164, 0.05);
}

.config-form-tab.active {
    color: var(--md-sys-color-primary);
    border-bottom-color: var(--md-sys-color-primary);
    background-color: transparent;
}

.config-form-tab-content {
    display: none;
    margin-top: 0;
}

.config-form-tab-content.active {
    display: block;
}

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
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    bottom: -2px;
    opacity: 0.8;
}

.sponsor-tab:hover {
    opacity: 1;
}

/* Patrocinador 1 - Azul */
.sponsor-tab-1 {
    background-color: #2196F3;
    border-bottom-color: #2196F3;
}
.sponsor-tab-1:hover {
    background-color: #1976D2;
}

/* Patrocinador 2 - Verde */
.sponsor-tab-2 {
    background-color: #4CAF50;
    border-bottom-color: #4CAF50;
}
.sponsor-tab-2:hover {
    background-color: #388E3C;
}

/* Patrocinador 3 - Naranja */
.sponsor-tab-3 {
    background-color: #FF9800;
    border-bottom-color: #FF9800;
}
.sponsor-tab-3:hover {
    background-color: #F57C00;
}

/* Patrocinador 4 - Púrpura */
.sponsor-tab-4 {
    background-color: #9C27B0;
    border-bottom-color: #9C27B0;
}
.sponsor-tab-4:hover {
    background-color: #7B1FA2;
}

.sponsor-tab.active {
    opacity: 1;
}

.sponsor-tab-content {
    margin-top: 0;
}
</style>

<script>
// Main Configuration Tabs
function switchConfigTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.config-form-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.config-form-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('config-form-tab-' + tabName);
    if (selectedContent) {
        selectedContent.classList.add('active');
    }
    
    // Add active class to selected tab
    const selectedTab = document.querySelector('.config-form-tab[data-tab="' + tabName + '"]');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}

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
