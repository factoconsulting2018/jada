<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ParallaxBackground $model */

$this->title = 'Fondo Parallax #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fondos Parallax', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parallax-background-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar este fondo parallax?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'section',
                    'value' => function ($model) {
                        return $model->getSectionLabel();
                    },
                ],
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => function ($model) {
                        if ($model->image) {
                            return Html::img($model->imageUrl, ['style' => 'max-width: 500px; max-height: 300px; border-radius: 8px;']);
                        }
                        return 'No hay imagen';
                    },
                ],
                'title',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                ],
                'position',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>


