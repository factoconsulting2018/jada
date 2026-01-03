<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\FooterMenuItem;
use app\models\Page;

/** @var yii\web\View $this */
/** @var app\models\FooterMenuItem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="footer-menu-item-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'position')->dropDownList([
                FooterMenuItem::POSITION_1 => 'Columna 1',
                FooterMenuItem::POSITION_2 => 'Columna 2',
                FooterMenuItem::POSITION_3 => 'Columna 3',
                FooterMenuItem::POSITION_4 => 'Columna 4',
            ], ['prompt' => 'Seleccione Columna', 'class' => 'form-control']) ?>

            <?= $form->field($model, 'order')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
            <p class="help-block">Orden dentro de la columna (0 = primero).</p>

            <?= $form->field($model, 'page_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(Page::find()->where(['status' => Page::STATUS_ACTIVE])->orderBy('title')->all(), 'id', 'title'),
                ['prompt' => 'Seleccione una página (opcional)', 'class' => 'form-control', 'id' => 'footermenuitem-page_id']
            ) ?>
            <p class="help-block">Si selecciona una página, se usará automáticamente el título y la URL de la página.</p>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'label')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            <p class="help-block">Etiqueta que aparecerá en el menú. Si seleccionó una página, se puede dejar vacío para usar el título de la página.</p>

            <?= $form->field($model, 'url')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'https://...']) ?>
            <p class="help-block">URL personalizada (solo si no seleccionó una página).</p>

            <?= $form->field($model, 'status')->dropDownList([
                FooterMenuItem::STATUS_ACTIVE => 'Activo',
                FooterMenuItem::STATUS_INACTIVE => 'Inactivo',
            ], ['class' => 'form-control']) ?>
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
    const pageSelect = document.getElementById('footermenuitem-page_id');
    const labelInput = document.getElementById('footermenuitem-label');
    const urlInput = document.getElementById('footermenuitem-url');
    
    if (pageSelect && labelInput) {
        // Load page title when page is selected
        pageSelect.addEventListener('change', function() {
            const selectedPageId = this.value;
            if (selectedPageId) {
                // Optionally fetch page title via AJAX, or just clear URL
                urlInput.value = '';
            }
        });
    }
});
</script>

