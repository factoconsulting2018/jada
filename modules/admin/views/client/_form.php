<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Client;

/** @var yii\web\View $this */
/** @var app\models\Client $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'id_type')->dropDownList([
    Client::ID_TYPE_FISICO => 'Físico',
    Client::ID_TYPE_JURIDICO => 'Jurídico',
], ['prompt' => 'Seleccione...', 'class' => 'form-control']) ?>

<?= $form->field($model, 'id_number')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Cédula']) ?>

<?= $form->field($model, 'full_name')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Nombre completo']) ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'class' => 'form-control', 'placeholder' => 'correo@ejemplo.com']) ?>

<?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Número de WhatsApp']) ?>

<?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Teléfono (opcional)']) ?>

<?= $form->field($model, 'address')->textarea(['rows' => 6, 'class' => 'form-control', 'placeholder' => 'Dirección (opcional)']) ?>

<?= $form->field($model, 'status')->dropDownList([
    Client::STATUS_PENDING => 'Pendiente',
    Client::STATUS_ACCEPTED => 'Aceptado',
    Client::STATUS_REJECTED => 'Rechazado',
], ['class' => 'form-control']) ?>

<div class="form-group">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
