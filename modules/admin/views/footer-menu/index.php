<?php

use yii\helpers\Html;
use app\models\FooterMenuItem;

/** @var yii\web\View $this */
/** @var array $groupedItems */

$this->title = 'Menú del Footer';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="footer-menu-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Nuevo Item', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>

        <p style="color: var(--md-sys-color-on-surface-variant); margin-bottom: 2rem;">
            Gestione los items del menú del footer. El menú se muestra en 4 columnas. Ordene los items dentro de cada columna.
        </p>

        <div class="footer-menu-columns" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-bottom: 2rem;">
            <?php for ($position = 1; $position <= 4; $position++): ?>
                <div class="footer-menu-column" style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--md-sys-color-primary);">
                        Columna <?= $position ?>
                    </h3>
                    <?php if (isset($groupedItems[$position]) && !empty($groupedItems[$position])): ?>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach ($groupedItems[$position] as $item): ?>
                                <li style="margin-bottom: 0.75rem; padding: 0.75rem; background: white; border-radius: 4px; display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <strong style="display: block; margin-bottom: 0.25rem;"><?= Html::encode($item->page && empty($item->label) ? $item->page->title : $item->label) ?></strong>
                                        <?php if ($item->page): ?>
                                            <small style="color: #666; display: block;">Página: <?= Html::encode($item->page->title) ?></small>
                                        <?php elseif ($item->url): ?>
                                            <small style="color: #666; display: block;">URL: <?= Html::encode($item->url) ?></small>
                                        <?php endif; ?>
                                        <?php if ($item->status == FooterMenuItem::STATUS_INACTIVE): ?>
                                            <span style="color: #999; font-size: 0.875rem;">(Inactivo)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                                        <?= Html::a('<span class="material-icons" style="font-size: 18px;">edit</span>', ['update', 'id' => $item->id], ['title' => 'Editar', 'style' => 'color: var(--md-sys-color-primary);']) ?>
                                        <?= Html::a('<span class="material-icons" style="font-size: 18px;">delete</span>', ['delete', 'id' => $item->id], [
                                            'title' => 'Eliminar',
                                            'data-confirm' => '¿Está seguro que desea eliminar este item?',
                                            'data-method' => 'post',
                                            'style' => 'color: #d32f2f;'
                                        ]) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="color: #999; font-style: italic; padding: 1rem; background: rgba(0,0,0,0.05); border-radius: 4px;">No hay items en esta columna</p>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<style>
@media (max-width: 1200px) {
    .footer-menu-columns {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .footer-menu-columns {
        grid-template-columns: 1fr !important;
    }
}
</style>

