<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var string $whatsappNumber */
/** @var string $siteTitle */
/** @var string $footerText */

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

        <?php $form = ActiveForm::begin(); ?>

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

        <div class="form-group" style="margin-top: 2rem;">
            <?= Html::submitButton('Guardar Configuración', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

