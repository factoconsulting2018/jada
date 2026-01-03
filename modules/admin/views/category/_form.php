<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Category $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

<?= $form->field($model, 'description')->textarea(['rows' => 6, 'class' => 'form-control']) ?>

<?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*', 'class' => 'form-control']) ?>

<?php if ($model->image): ?>
    <div class="form-group">
        <label class="form-label">Imagen Actual</label>
        <div>
            <?= Html::img($model->imageUrl, ['style' => 'max-width: 200px; margin-bottom: 1rem;']) ?>
        </div>
    </div>
<?php endif; ?>

<?= $form->field($model, 'status')->dropDownList([
    \app\models\Category::STATUS_ACTIVE => 'Activo',
    \app\models\Category::STATUS_INACTIVE => 'Inactivo',
], ['class' => 'form-control']) ?>

<div class="form-group">
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

