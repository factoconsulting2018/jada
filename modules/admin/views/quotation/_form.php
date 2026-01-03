<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'product_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(\app\models\Product::find()->all(), 'id', 'name'),
    ['prompt' => 'Seleccione un producto', 'class' => 'form-control']
) ?>

<?= $form->field($model, 'id_type')->dropDownList([
    'fisico' => 'Físico',
    'juridico' => 'Jurídico',
], ['prompt' => 'Seleccione el tipo', 'class' => 'form-control']) ?>

<?= $form->field($model, 'id_number')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'full_name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'class' => 'form-control']) ?>

<?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'status')->dropDownList([
    \app\models\Quotation::STATUS_NEW => 'Nueva',
    \app\models\Quotation::STATUS_PROCESSED => 'Procesada',
], ['class' => 'form-control']) ?>

<div class="form-group">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
    <?php if (!$model->isNewRecord): ?>
        <?= Html::submitButton('Guardar y Reenviar Email', ['class' => 'btn btn-success', 'name' => 'resend_email', 'value' => '1']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>

