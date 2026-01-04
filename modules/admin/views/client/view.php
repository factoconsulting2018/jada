<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Client;

/** @var yii\web\View $this */
/** @var app\models\Client $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar este cliente?',
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
                    'attribute' => 'id_type',
                    'value' => $model->getIdTypeLabel(),
                ],
                'id_number',
                'full_name',
                'email:email',
                'whatsapp',
                'phone',
                'address:ntext',
                [
                    'attribute' => 'status',
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
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>

<style>
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
