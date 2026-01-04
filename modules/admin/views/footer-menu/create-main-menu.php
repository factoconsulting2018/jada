<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MainMenuItem $model */

$this->title = 'Nuevo Item del Menú Principal';
$this->params['breadcrumbs'][] = ['label' => 'Menús', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="main-menu-item-create">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form-main-menu', [
            'model' => $model,
            'pageList' => $pageList,
        ]) ?>
    </div>
</div>

