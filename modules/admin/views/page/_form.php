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

            <?= $form->field($model, 'show_in_menu')->checkbox(['label' => 'Mostrar en el menú principal']) ?>

            <?= $form->field($model, 'menu_order')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
            <p class="help-block">Orden en el menú (0 = primero).</p>
        </div>
    </div>

    <div class="form-group" style="margin-top: 2rem;">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

