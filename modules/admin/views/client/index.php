<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Client;

/** @var yii\web\View $this */
/** @var app\models\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $currentTab */

$this->title = 'Clientes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nuevo Cliente', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" style="margin-bottom: 2rem; border-bottom: 2px solid #e0e0e0;">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'pending' ? 'active' : '' ?>" href="<?= Url::to(['index', 'tab' => 'pending']) ?>">
                    Pendientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'accepted' ? 'active' : '' ?>" href="<?= Url::to(['index', 'tab' => 'accepted']) ?>">
                    Clientes Aceptados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'rejected' ? 'active' : '' ?>" href="<?= Url::to(['index', 'tab' => 'rejected']) ?>">
                    Rechazados
                </a>
            </li>
        </ul>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'admin-table'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'id_number',
                    'label' => 'Cédula',
                ],
                [
                    'attribute' => 'full_name',
                    'label' => 'Nombre Completo',
                ],
                'email:email',
                'whatsapp',
                [
                    'attribute' => 'status',
                    'label' => 'Estado',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $statuses = [
                            Client::STATUS_PENDING => ['label' => 'Pendiente', 'class' => 'badge-warning'],
                            Client::STATUS_ACCEPTED => ['label' => 'Aceptado', 'class' => 'badge-success'],
                            Client::STATUS_REJECTED => ['label' => 'Rechazado', 'class' => 'badge-danger'],
                        ];
                        $status = $statuses[$model->status] ?? ['label' => 'Desconocido', 'class' => 'badge-secondary'];
                        return '<span class="badge ' . $status['class'] . '">' . $status['label'] . '</span>';
                    },
                    'filter' => [
                        Client::STATUS_PENDING => 'Pendiente',
                        Client::STATUS_ACCEPTED => 'Aceptado',
                        Client::STATUS_REJECTED => 'Rechazado',
                    ],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Acciones',
                    'template' => '{accept} {pending} {reject} {view} {update} {delete}',
                    'buttons' => [
                        'accept' => function ($url, $model) {
                            if ($model->status == Client::STATUS_ACCEPTED) {
                                return '';
                            }
                            return Html::a('<span class="material-icons" style="font-size: 18px; color: #28a745;" title="Aceptar">check_circle</span>', 
                                ['change-status', 'id' => $model->id, 'status' => Client::STATUS_ACCEPTED], 
                                [
                                    'data-confirm' => '¿Desea cambiar el estado a Aceptado?',
                                    'data-method' => 'post',
                                ]
                            );
                        },
                        'pending' => function ($url, $model) {
                            if ($model->status == Client::STATUS_PENDING) {
                                return '';
                            }
                            return Html::a('<span class="material-icons" style="font-size: 18px; color: #ffc107;" title="Pendiente">schedule</span>', 
                                ['change-status', 'id' => $model->id, 'status' => Client::STATUS_PENDING], 
                                [
                                    'data-confirm' => '¿Desea cambiar el estado a Pendiente?',
                                    'data-method' => 'post',
                                ]
                            );
                        },
                        'reject' => function ($url, $model) {
                            if ($model->status == Client::STATUS_REJECTED) {
                                return '';
                            }
                            return Html::a('<span class="material-icons" style="font-size: 18px; color: #dc3545;" title="Rechazar">cancel</span>', 
                                ['change-status', 'id' => $model->id, 'status' => Client::STATUS_REJECTED], 
                                [
                                    'data-confirm' => '¿Desea cambiar el estado a Rechazado?',
                                    'data-method' => 'post',
                                ]
                            );
                        },
                        'view' => function ($url, $model) {
                            return Html::a('<span class="material-icons">visibility</span>', $url, ['title' => 'Ver']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="material-icons">edit</span>', $url, ['title' => 'Editar']);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="material-icons">delete</span>', $url, [
                                'title' => 'Eliminar',
                                'data-confirm' => '¿Está seguro que desea eliminar este cliente?',
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

<style>
.nav-tabs {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-right: 0.5rem;
}

.nav-link {
    display: block;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    color: var(--md-sys-color-on-surface-variant);
    border: 1px solid transparent;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    transition: all 0.3s;
}

.nav-link:hover {
    background-color: #f5f5f5;
    color: var(--md-sys-color-on-surface);
}

.nav-link.active {
    color: var(--md-sys-color-primary);
    background-color: white;
    border-color: #e0e0e0;
    border-bottom-color: white;
    font-weight: 500;
    position: relative;
    bottom: -2px;
}

.badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}
</style>
