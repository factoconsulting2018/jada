<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Banner $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'subtitle')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*', 'class' => 'form-control']) ?>

<?php if ($model->image): ?>
    <div class="form-group">
        <label class="form-label">Imagen Actual</label>
        <div>
            <?= Html::img($model->imageUrl, ['style' => 'max-width: 500px; margin-bottom: 1rem;']) ?>
        </div>
    </div>
<?php endif; ?>

<?= $form->field($model, 'link')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
<p class="help-block">URL opcional a donde redirigir al hacer clic en el banner.</p>

<?= $form->field($model, 'order')->textInput(['type' => 'number', 'class' => 'form-control']) ?>
<p class="help-block">Orden de visualización (menor número = aparece primero).</p>

<?= $form->field($model, 'status')->dropDownList([
    \app\models\Banner::STATUS_ACTIVE => 'Activo',
    \app\models\Banner::STATUS_INACTIVE => 'Inactivo',
], ['class' => 'form-control']) ?>

<div class="form-group">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

