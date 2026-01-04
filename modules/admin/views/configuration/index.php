<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SponsorBanner;
use app\models\ParallaxBackground;
use app\models\Configuration;

/** @var yii\web\View $this */
/** @var string $whatsappNumber */
/** @var string $siteTitle */
/** @var string $footerText */
/** @var string $dollarPrice */
/** @var string $showDollarPrice */
/** @var app\models\SponsorBanner[] $sponsorBanners */
/** @var array $parallaxSections */
/** @var array $parallaxBackgroundsBySection */

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
                <button type="button" class="config-form-tab" data-tab="sections" onclick="switchConfigTab('sections')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">view_module</span>
                    Secciones
                </button>
                <button type="button" class="config-form-tab" data-tab="bloquesx" onclick="switchConfigTab('bloquesx')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">widgets</span>
                    BloquesX
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
                        Gestiona los fondos parallax para las distintas secciones del sitio.
                    </p>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="parallax_section_select" class="form-label" style="font-weight: 500; margin-bottom: 0.75rem; display: block;">Seleccionar Sección</label>
                        <select id="parallax_section_select" class="form-control" style="max-width: 300px;" onchange="switchParallaxSection(this.value)">
                            <?php foreach ($parallaxSections as $sectionKey => $sectionLabel): ?>
                                <option value="<?= Html::encode($sectionKey) ?>"><?= Html::encode($sectionLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                            Selecciona la sección para la cual deseas configurar los fondos parallax.
                        </small>
                    </div>

                    <?php 
                    $firstSectionKey = null;
                    foreach ($parallaxSections as $sectionKey => $sectionLabel) {
                        if ($firstSectionKey === null) {
                            $firstSectionKey = $sectionKey;
                        }
                    }
                    ?>
                    <?php foreach ($parallaxSections as $sectionKey => $sectionLabel): ?>
                        <div class="parallax-section-content" id="parallax-section-<?= Html::encode($sectionKey) ?>" style="<?= $sectionKey !== $firstSectionKey ? 'display: none;' : '' ?>">
                            <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 1.5rem; color: var(--md-sys-color-on-surface);">
                                <?= Html::encode($sectionLabel) ?>
                            </h3>
                            <div class="sponsor-tabs-container">
                                <div class="sponsor-tabs">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <button type="button" 
                                                class="sponsor-tab <?= $i === 1 ? 'active' : '' ?>" 
                                                data-tab="parallax-<?= Html::encode($sectionKey) ?>-<?= $i ?>"
                                                onclick="switchParallaxTab('<?= Html::encode($sectionKey) ?>', <?= $i ?>)">
                                            Fondo <?= $i ?>
                                        </button>
                                    <?php endfor; ?>
                                </div>

                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php $bg = $parallaxBackgroundsBySection[$sectionKey][$i]; ?>
                                    <div class="sponsor-tab-content" id="parallax-<?= Html::encode($sectionKey) ?>-tab-<?= $i ?>" style="<?= $i !== 1 ? 'display: none;' : '' ?>">
                                        <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                                            <div class="form-group">
                                                <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 1rem;">
                                                    <input type="checkbox" 
                                                           name="parallax_status_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                           value="1"
                                                           <?= $bg->status == ParallaxBackground::STATUS_ACTIVE ? 'checked' : '' ?>
                                                           style="margin-right: 0.5rem; width: auto;">
                                                    <span>Activar este fondo</span>
                                                </label>
                                            </div>

                                            <div class="form-group" style="margin-top: 1rem;">
                                                <label for="parallax_image_<?= Html::encode($sectionKey) ?>_<?= $i ?>" class="form-label">Imagen de Fondo</label>
                                                <?php if ($bg->image): ?>
                                                    <div style="margin-bottom: 0.5rem;">
                                                        <img src="<?= Html::encode($bg->imageUrl) ?>" 
                                                             alt="Fondo parallax <?= Html::encode($sectionLabel) ?> <?= $i ?>" 
                                                             style="max-width: 300px; max-height: 200px; object-fit: cover; border: 1px solid #e0e0e0; padding: 0.5rem; background: #f5f5f5; border-radius: 4px;">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" 
                                                       id="parallax_image_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                       name="parallax_image_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                       accept="image/png,image/jpeg,image/jpg,image/webp"
                                                       class="form-control">
                                                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                                                    Formatos: PNG, JPG, JPEG, WEBP. Tamaño máximo: 10MB.
                                                </small>
                                            </div>

                                            <div class="form-group" style="margin-top: 1.5rem;">
                                                <label for="parallax_overlay_color_<?= Html::encode($sectionKey) ?>_<?= $i ?>" class="form-label">Color del Overlay</label>
                                                <div style="display: flex; gap: 1rem; align-items: center;">
                                                    <input type="color" 
                                                           id="parallax_overlay_color_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                           name="parallax_overlay_color_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                           value="<?= Html::encode($bg->overlay_color ?: '#FFFFFF') ?>"
                                                           style="width: 80px; height: 40px; border: 1px solid #e0e0e0; border-radius: 4px; cursor: pointer;"
                                                           onchange="document.getElementById('parallax_overlay_color_text_<?= Html::encode($sectionKey) ?>_<?= $i ?>').value = this.value;">
                                                    <input type="text" 
                                                           id="parallax_overlay_color_text_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                           value="<?= Html::encode($bg->overlay_color ?: '#FFFFFF') ?>"
                                                           placeholder="#FFFFFF"
                                                           maxlength="7"
                                                           style="flex: 1; padding: 0.5rem; border: 1px solid #e0e0e0; border-radius: 4px;"
                                                           onchange="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('parallax_overlay_color_<?= Html::encode($sectionKey) ?>_<?= $i ?>').value = this.value; }">
                                                </div>
                                                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                                                    Color que se aplicará como overlay sobre la imagen para regular el contraste. Por defecto: blanco (#FFFFFF).
                                                </small>
                                            </div>

                                            <div class="form-group" style="margin-top: 1rem;">
                                                <label for="parallax_overlay_opacity_<?= Html::encode($sectionKey) ?>_<?= $i ?>" class="form-label">Opacidad del Overlay</label>
                                                <input type="number" 
                                                       id="parallax_overlay_opacity_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                       name="parallax_overlay_opacity_<?= Html::encode($sectionKey) ?>_<?= $i ?>" 
                                                       value="<?= Html::encode($bg->overlay_opacity ?: '0.3') ?>"
                                                       min="0" 
                                                       max="1" 
                                                       step="0.01"
                                                       class="form-control"
                                                       style="max-width: 200px;">
                                                <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                                                    Opacidad del overlay (0.00 = transparente, 1.00 = completamente opaco). Valor por defecto: 0.3
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tab 6: Secciones -->
            <div class="config-form-tab-content" id="config-form-tab-sections">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                    <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1.5rem;">
                        Activa o desactiva las distintas secciones que aparecen en la página de inicio.
                    </p>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" 
                                   id="section_banner" 
                                   name="section_banner" 
                                   value="1"
                                   <?= Configuration::getValue('section_banner', '1') == '1' ? 'checked' : '' ?>
                                   style="margin-right: 0.5rem; width: auto;">
                            <span style="font-weight: 500;">Sección Banner (Hero)</span>
                        </label>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block; margin-left: 1.75rem;">
                            Muestra el banner principal con las imágenes hero en la parte superior de la página.
                        </small>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" 
                                   id="section_products" 
                                   name="section_products" 
                                   value="1"
                                   <?= Configuration::getValue('section_products', '1') == '1' ? 'checked' : '' ?>
                                   style="margin-right: 0.5rem; width: auto;">
                            <span style="font-weight: 500;">Sección Productos</span>
                        </label>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block; margin-left: 1.75rem;">
                            Muestra la sección "Nuestros productos" con el carrusel de productos destacados.
                        </small>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" 
                                   id="section_categories" 
                                   name="section_categories" 
                                   value="1"
                                   <?= Configuration::getValue('section_categories', '1') == '1' ? 'checked' : '' ?>
                                   style="margin-right: 0.5rem; width: auto;">
                            <span style="font-weight: 500;">Sección Categorías</span>
                        </label>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block; margin-left: 1.75rem;">
                            Muestra la sección de categorías con las tarjetas de categorías disponibles.
                        </small>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" 
                                   id="section_sponsors" 
                                   name="section_sponsors" 
                                   value="1"
                                   <?= Configuration::getValue('section_sponsors', '1') == '1' ? 'checked' : '' ?>
                                   style="margin-right: 0.5rem; width: auto;">
                            <span style="font-weight: 500;">Sección Marcas</span>
                        </label>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block; margin-left: 1.75rem;">
                            Muestra la sección "Nuestras marcas" con los banners de patrocinadores.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Tab 7: BloquesX -->
            <div class="config-form-tab-content" id="config-form-tab-bloquesx">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                    <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 1.5rem;">
                        Gestiona el bloque de contenido que aparece entre el banner principal y la sección de productos.
                    </p>

                    <div class="form-group">
                        <label for="block_title" class="form-label">Título del Bloque</label>
                        <input type="text" 
                               id="block_title" 
                               name="block_title" 
                               class="form-control" 
                               value="<?= Html::encode($blockTitle) ?>" 
                               placeholder="Título del bloque"
                               maxlength="255">
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                            Título que aparecerá en el bloque de contenido.
                        </small>
                    </div>

                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="block_content" class="form-label">Contenido del Bloque</label>
                        <textarea id="block_content" 
                                  name="block_content" 
                                  class="form-control" 
                                  rows="6"
                                  placeholder="Ingrese el contenido del bloque..."><?= Html::encode($blockContent) ?></textarea>
                        <small class="form-text" style="color: var(--md-sys-color-on-surface-variant); margin-top: 0.5rem; display: block;">
                            Contenido de texto que aparecerá en el bloque. Puede incluir múltiples líneas.
                        </small>
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

function switchParallaxSection(sectionKey) {
    // Hide all section contents
    document.querySelectorAll('.parallax-section-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Show selected section
    const selectedSection = document.getElementById('parallax-section-' + sectionKey);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        // Reset to first tab in the section
        const firstTab = selectedSection.querySelector('.sponsor-tab');
        if (firstTab) {
            const firstTabNumber = firstTab.getAttribute('data-tab').split('-').pop();
            switchParallaxTab(sectionKey, parseInt(firstTabNumber));
        }
    }
}

function switchParallaxTab(sectionKey, tabNumber) {
    // Hide all parallax tab contents for this section
    const sectionContent = document.getElementById('parallax-section-' + sectionKey);
    if (!sectionContent) return;
    
    for (let i = 1; i <= 4; i++) {
        const content = sectionContent.querySelector('#parallax-' + sectionKey + '-tab-' + i);
        if (content) {
            content.style.display = 'none';
        }
        const tab = sectionContent.querySelector('.sponsor-tab[data-tab="parallax-' + sectionKey + '-' + i + '"]');
        if (tab) {
            tab.classList.remove('active');
        }
    }
    
    // Show selected tab content
    const selectedContent = sectionContent.querySelector('#parallax-' + sectionKey + '-tab-' + tabNumber);
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }
    
    // Add active class to selected tab
    const selectedTab = sectionContent.querySelector('.sponsor-tab[data-tab="parallax-' + sectionKey + '-' + tabNumber + '"]');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}
</script>
