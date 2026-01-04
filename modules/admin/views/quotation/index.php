<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $search */

$this->title = 'Cotizaciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nueva Cotización', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <div style="margin-bottom: 2rem;">
            <form method="get" action="<?= \yii\helpers\Url::to(['index']) ?>" style="display: flex; gap: 1rem; align-items: center;">
                <input type="text" 
                       name="search" 
                       value="<?= Html::encode($search) ?>" 
                       placeholder="Buscar por nombre, email, cédula o WhatsApp..." 
                       class="form-control" 
                       style="flex: 1; max-width: 500px;">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'admin-table'],
            'summary' => 'Mostrando {begin}-{end} de {totalCount} elementos.',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'label' => 'Productos',
                    'value' => function ($model) {
                        $products = [];
                        foreach ($model->quotationProducts as $qp) {
                            $products[] = $qp->product->name . ' (x' . $qp->quantity . ')';
                        }
                        return !empty($products) ? implode(', ', $products) : 'Sin productos';
                    },
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'label' => 'Total',
                    'value' => function ($model) {
                        return '₡' . number_format($model->getTotal(), 2, '.', ',');
                    },
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'full_name',
                    'label' => 'Cliente',
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'whatsapp',
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'id_type',
                    'label' => 'Tipo ID',
                    'value' => function ($model) {
                        return $model->getIdTypeLabel();
                    },
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Estado',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Fecha',
                    'value' => function ($model) {
                        if ($model->created_at) {
                            $meses = [
                                'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                                'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                                'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                                'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
                            ];
                            // Handle both timestamp and datetime string
                            $timestamp = is_numeric($model->created_at) ? (int)$model->created_at : strtotime($model->created_at);
                            if ($timestamp === false) {
                                return $model->created_at;
                            }
                            $dateStr = date('F j, Y g:i:s A', $timestamp);
                            foreach ($meses as $en => $es) {
                                $dateStr = str_replace($en, $es, $dateStr);
                            }
                            return $dateStr;
                        }
                        return '';
                    },
                    'contentOptions' => ['style' => 'text-align: left;'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Acciones',
                    'template' => '{view} {update} {delete}',
                    'contentOptions' => ['style' => 'text-align: left;'],
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
                                'data-confirm' => '¿Está seguro que desea eliminar esta cotización?',
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

