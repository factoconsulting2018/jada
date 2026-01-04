<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Brand $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="brand-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        \app\models\Brand::STATUS_ACTIVE => 'Activo',
        \app\models\Brand::STATUS_INACTIVE => 'Inactivo',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

