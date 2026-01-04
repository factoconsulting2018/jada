<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;

$searchQuery = Yii::$app->request->get('ProductSearch')['name'] ?? '';
?>
<div class="product-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nuevo Producto', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'options' => ['class' => 'product-search-form', 'style' => 'margin-bottom: 2rem;']
        ]); ?>

        <div style="display: flex; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; max-width: 400px;">
                <label for="product-search-input" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar productos</label>
                <input 
                    type="text" 
                    id="product-search-input" 
                    name="ProductSearch[name]" 
                    class="form-control" 
                    value="<?= Html::encode($searchQuery) ?>" 
                    placeholder="Buscar por nombre..."
                    autocomplete="off"
                >
            </div>
            <div>
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
                <?php if ($searchQuery): ?>
                    <?= Html::a('Limpiar', ['index'], ['class' => 'btn btn-secondary']) ?>
                <?php endif; ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'admin-table'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => function ($model) {
                        if ($model->image) {
                            return Html::img($model->imageUrl, ['style' => 'width: 50px; height: 50px; object-fit: cover; border-radius: 4px;']);
                        }
                        return '';
                    },
                    'contentOptions' => ['style' => 'width: 80px;'],
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $html = Html::encode($model->name);
                        if ($model->code) {
                            $html .= '<br><small style="color: #666;">Código: ' . Html::encode($model->code) . '</small>';
                        }
                        return $html;
                    },
                ],
                [
                    'attribute' => 'category_id',
                    'value' => 'category.name',
                ],
                [
                    'attribute' => 'brand_id',
                    'value' => function ($model) {
                        return $model->brand ? $model->brand->name : 'Sin marca';
                    },
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return '₡' . number_format($model->price, 2, '.', ',');
                    },
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status == \app\models\Product::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Acciones',
                    'template' => '{view} {update} {backup} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="material-icons">visibility</span>', $url, ['title' => 'Ver']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="material-icons">edit</span>', $url, ['title' => 'Editar']);
                        },
                        'backup' => function ($url, $model) {
                            return Html::a('<span class="material-icons">download</span>', ['backup', 'id' => $model->id], [
                                'title' => 'Backup',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="material-icons">delete</span>', $url, [
                                'title' => 'Eliminar',
                                'data-confirm' => '¿Está seguro que desea eliminar este producto?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>

