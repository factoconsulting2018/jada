<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\MainMenuItem;

/** @var yii\web\View $this */
/** @var app\models\MainMenuItem $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $pageList */
?>

<div class="main-menu-item-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList([
                MainMenuItem::TYPE_LINK => 'Enlace',
                MainMenuItem::TYPE_PAGE => 'Página',
            ], ['prompt' => 'Seleccione Tipo', 'class' => 'form-control', 'id' => 'mainmenuitem-type']) ?>

            <?= $form->field($model, 'label')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            <p class="help-block">Etiqueta que aparecerá en el menú.</p>

            <div id="url-field">
                <?= $form->field($model, 'url')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => '/ o /products, etc.']) ?>
                <p class="help-block">URL del enlace (ej: /, /products, /quotation).</p>
            </div>

            <div id="page-field" style="display: none;">
                <?= $form->field($model, 'page_id')->dropDownList(
                    $pageList,
                    ['prompt' => 'Seleccione una página', 'class' => 'form-control']
                ) ?>
                <p class="help-block">Si selecciona una página, se usará automáticamente el título de la página.</p>
            </div>

            <?= $form->field($model, 'identifier')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'home, products, quotation, admin']) ?>
            <p class="help-block">Identificador único (opcional, para enlaces estándar como home, products, etc.).</p>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'order')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
            <p class="help-block">Orden en el menú (0 = primero).</p>

            <?= $form->field($model, 'status')->dropDownList([
                MainMenuItem::STATUS_ACTIVE => 'Activo',
                MainMenuItem::STATUS_INACTIVE => 'Inactivo',
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
    var typeField = document.getElementById('mainmenuitem-type');
    var urlField = document.getElementById('url-field');
    var pageField = document.getElementById('page-field');
    
    function toggleFields() {
        if (typeField.value === '<?= MainMenuItem::TYPE_PAGE ?>') {
            urlField.style.display = 'none';
            pageField.style.display = 'block';
        } else {
            urlField.style.display = 'block';
            pageField.style.display = 'none';
        }
    }
    
    typeField.addEventListener('change', toggleFields);
    toggleFields(); // Initial call
});
</script>

