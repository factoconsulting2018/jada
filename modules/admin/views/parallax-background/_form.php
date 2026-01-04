<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ParallaxBackground;

/** @var yii\web\View $this */
/** @var app\models\ParallaxBackground $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="parallax-background-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'section')->dropDownList(ParallaxBackground::getSections(), ['prompt' => 'Seleccione una sección...']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
    
    <?php if ($model->image): ?>
        <div style="margin-bottom: 1rem;">
            <label class="control-label">Imagen Actual</label>
            <div>
                <?= Html::img($model->imageUrl, ['style' => 'max-width: 300px; max-height: 200px; border-radius: 8px;']) ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'status')->dropDownList([
        ParallaxBackground::STATUS_INACTIVE => 'Inactivo',
        ParallaxBackground::STATUS_ACTIVE => 'Activo',
    ]) ?>

    <?= $form->field($model, 'position')->textInput(['type' => 'number']) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

