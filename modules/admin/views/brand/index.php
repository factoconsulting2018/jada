<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $searchQuery */

$this->title = 'Marcas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nueva Marca', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'options' => ['class' => 'brand-search-form', 'style' => 'margin-bottom: 2rem;']
        ]); ?>

        <div style="display: flex; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; max-width: 400px;">
                <label for="brand-search-input" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar marcas</label>
                <input 
                    type="text" 
                    id="brand-search-input" 
                    name="search" 
                    class="form-control" 
                    value="<?= Html::encode($searchQuery ?? '') ?>" 
                    placeholder="Buscar por nombre..."
                    autocomplete="off"
                >
            </div>
            <div>
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
                <?php if (!empty($searchQuery)): ?>
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
                'name',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->status == \app\models\Brand::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->created_at) {
                            $months = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                            $timestamp = is_numeric($model->created_at) ? $model->created_at : strtotime($model->created_at);
                            $date = getdate($timestamp);
                            $day = $date['mday'];
                            $month = $months[(int)$date['mon']];
                            $year = $date['year'];
                            $hour = str_pad($date['hours'], 2, '0', STR_PAD_LEFT);
                            $minute = str_pad($date['minutes'], 2, '0', STR_PAD_LEFT);
                            return $month . ' ' . $day . ', ' . $year . ' ' . $hour . ':' . $minute;
                        }
                        return '';
                    },
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->updated_at) {
                            $months = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                            $timestamp = is_numeric($model->updated_at) ? $model->updated_at : strtotime($model->updated_at);
                            $date = getdate($timestamp);
                            $day = $date['mday'];
                            $month = $months[(int)$date['mon']];
                            $year = $date['year'];
                            $hour = str_pad($date['hours'], 2, '0', STR_PAD_LEFT);
                            $minute = str_pad($date['minutes'], 2, '0', STR_PAD_LEFT);
                            return $month . ' ' . $day . ', ' . $year . ' ' . $hour . ':' . $minute;
                        }
                        return '';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Acciones',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="material-icons">visibility</span>', $url, ['title' => 'Ver']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="material-icons">edit</span>', $url, ['title' => 'Editar']);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="material-icons">delete</span>', $url, [
                                'title' => 'Eliminar',
                                'data-confirm' => '¿Está seguro que desea eliminar esta marca?',
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
