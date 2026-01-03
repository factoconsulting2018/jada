<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use app\models\Page;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Páginas';
$this->params['breadcrumbs'][] = $this->title;

$searchQuery = Yii::$app->request->get('search', '');
?>
<div class="page-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nueva Página', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'options' => ['class' => 'page-search-form', 'style' => 'margin-bottom: 2rem;']
        ]); ?>

        <div style="display: flex; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; max-width: 400px;">
                <label for="search-input" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar páginas</label>
                <input 
                    type="text" 
                    id="search-input" 
                    name="search" 
                    class="form-control" 
                    value="<?= Html::encode($searchQuery) ?>" 
                    placeholder="Buscar por título o slug..."
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
                'title',
                'slug',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                ],
                [
                    'attribute' => 'show_in_menu',
                    'value' => function ($model) {
                        return $model->show_in_menu ? 'Sí' : 'No';
                    },
                ],
                'menu_order',
                'created_at:datetime',
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
                                'data-confirm' => '¿Está seguro que desea eliminar esta página?',
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

