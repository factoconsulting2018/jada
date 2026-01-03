<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Page $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Páginas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar esta página?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title',
                'slug',
                [
                    'attribute' => 'content',
                    'format' => 'html',
                    'value' => nl2br(Html::encode($model->content)),
                ],
                [
                    'attribute' => 'status',
                    'value' => $model->getStatusLabel(),
                ],
                [
                    'attribute' => 'show_in_menu',
                    'value' => $model->show_in_menu ? 'Sí' : 'No',
                ],
                'menu_order',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

        <div style="margin-top: 2rem; padding: 1.5rem; background: #f5f5f5; border-radius: 8px;">
            <h3 style="margin-top: 0;">Menú del Footer</h3>
            <?php
            $footerItems = $model->footerMenuItems;
            if (!empty($footerItems)):
            ?>
                <p>Esta página está presente en el menú del footer:</p>
                <ul>
                    <?php foreach ($footerItems as $item): ?>
                        <li>
                            <?= Html::encode($item->getPositionLabel()) ?> - 
                            <?= Html::a('Ver/Editar', ['/admin/footer-menu/update', 'id' => $item->id], ['class' => 'btn btn-sm btn-secondary']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Esta página no está en el menú del footer.</p>
            <?php endif; ?>
            
            <div style="margin-top: 1rem;">
                <?= Html::a('Agregar al Menú del Footer', ['/admin/footer-menu/create', 'page_id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>

