<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Page;

/** @var yii\web\View $this */
/** @var app\models\Page $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="page-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            <p class="help-block">URL amigable (solo letras minúsculas, números y guiones). Si se deja vacío, se generará automáticamente desde el título.</p>

            <?= $form->field($model, 'content')->textarea(['rows' => 15, 'class' => 'form-control', 'style' => 'resize: vertical; max-height: 600px;']) ?>
            <p class="help-block">Contenido de la página (soporta HTML básico).</p>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'status')->dropDownList([
                Page::STATUS_ACTIVE => 'Activo',
                Page::STATUS_INACTIVE => 'Inactivo',
            ], ['class' => 'form-control']) ?>

            <div class="form-group field-page-add_to_menu">
                <label class="control-label" for="page-add_to_menu">Agregar al Menú</label>
                <select id="page-add_to_menu" name="Page[add_to_menu]" class="form-control">
                    <?php
                    $isInMain = $model->show_in_menu || ($model->id && $model->getMainMenuItem());
                    $isInFooter = $model->id ? $model->isInFooterMenu() : false;
                    $currentSelection = 'none';
                    if ($isInMain && $isInFooter) {
                        $currentSelection = 'both';
                    } elseif ($isInMain) {
                        $currentSelection = 'main';
                    } elseif ($isInFooter) {
                        $currentSelection = 'footer';
                    }
                    ?>
                    <option value="none" <?= ($currentSelection == 'none') ? 'selected' : '' ?>>Ninguno</option>
                    <option value="main" <?= ($currentSelection == 'main') ? 'selected' : '' ?>>Menú Principal</option>
                    <option value="footer" <?= ($currentSelection == 'footer') ? 'selected' : '' ?>>Menú Footer</option>
                    <option value="both" <?= ($currentSelection == 'both') ? 'selected' : '' ?>>Ambos</option>
                </select>
                <div class="help-block">Seleccione a qué menú desea agregar esta página.</div>
            </div>

            <?= $form->field($model, 'menu_order')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
            <p class="help-block">Orden en el menú (0 = primero).</p>

            <div id="footer-menu-position-field" style="display: <?= (in_array($currentSelection, ['footer', 'both'])) ? 'block' : 'none' ?>;">
                <div class="form-group">
                    <label class="control-label" for="page-footer_position">Posición en Menú Footer</label>
                    <select id="page-footer_position" name="Page[footer_position]" class="form-control">
                        <?php
                        $footerPosition = $model->id ? $model->getFooterMenuPosition() : 1;
                        ?>
                        <option value="1" <?= ($footerPosition == 1) ? 'selected' : '' ?>>Columna 1</option>
                        <option value="2" <?= ($footerPosition == 2) ? 'selected' : '' ?>>Columna 2</option>
                        <option value="3" <?= ($footerPosition == 3) ? 'selected' : '' ?>>Columna 3</option>
                        <option value="4" <?= ($footerPosition == 4) ? 'selected' : '' ?>>Columna 4</option>
                    </select>
                    <div class="help-block">Seleccione la columna del menú footer donde aparecerá esta página.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" style="margin-top: 2rem;">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var addToMenuField = document.getElementById('page-add_to_menu');
    var footerPositionField = document.getElementById('footer-menu-position-field');
    
    function toggleFooterPosition() {
        if (addToMenuField.value === 'footer' || addToMenuField.value === 'both') {
            footerPositionField.style.display = 'block';
        } else {
            footerPositionField.style.display = 'none';
        }
    }
    
    if (addToMenuField) {
        addToMenuField.addEventListener('change', toggleFooterPosition);
        toggleFooterPosition(); // Initial call
    }
});
</script>

