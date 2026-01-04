<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = 'Nuevo Producto';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
            'categoryList' => $categoryList,
            'brandList' => $brandList,
        ]) ?>
    </div>
</div>

