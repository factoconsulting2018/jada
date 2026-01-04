<?php

use yii\helpers\Html;
use app\models\FooterMenuItem;
use app\models\MainMenuItem;

/** @var yii\web\View $this */
/** @var array $groupedItems */
/** @var array $mainMenuItems */

$this->title = 'Menús';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menus-index">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <!-- Tabs Container -->
        <div class="menus-tabs-container">
            <div class="menus-tabs">
                <button type="button" class="menus-tab menus-tab-main active" data-tab="main" onclick="switchMenusTab('main')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">menu</span>
                    Menú Principal
                </button>
                <button type="button" class="menus-tab menus-tab-footer" data-tab="footer" onclick="switchMenusTab('footer')">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.5rem;">list</span>
                    Menú Footer
                </button>
            </div>

            <!-- Tab 1: Menú Principal -->
            <div class="menus-tab-content active" id="menus-tab-main">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <p style="color: var(--md-sys-color-on-surface-variant); margin: 0;">
                            Arrastre y suelte los items para ordenarlos en el menú principal del sitio.
                        </p>
                        <?= Html::a('Nuevo Item', ['create-main-menu'], ['class' => 'btn btn-primary']) ?>
                    </div>

                    <ul id="main-menu-sortable" class="sortable-list" style="list-style: none; padding: 0; margin: 0;">
                        <?php if (!empty($mainMenuItems)): ?>
                            <?php foreach ($mainMenuItems as $item): ?>
                                <li class="sortable-item" data-item-id="<?= $item->id ?>" style="padding: 1rem; background: white; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 0.5rem; cursor: move; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;">
                                        <strong style="display: block; margin-bottom: 0.25rem;"><?= Html::encode($item->label) ?></strong>
                                        <small style="color: #666; display: block;">
                                            <?php if ($item->type === MainMenuItem::TYPE_PAGE && $item->page): ?>
                                                Página: <?= Html::encode($item->page->title) ?> (<?= Html::encode($item->page->slug) ?>)
                                            <?php else: ?>
                                                URL: <?= Html::encode($item->url) ?>
                                                <?php if ($item->identifier): ?>
                                                    (<?= Html::encode($item->identifier) ?>)
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </small>
                                        <?php if ($item->status == MainMenuItem::STATUS_INACTIVE): ?>
                                            <span style="color: #999; font-size: 0.875rem;">(Inactivo)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; flex-shrink: 0; align-items: center;">
                                        <span class="drag-handle material-icons" style="color: #999; font-size: 20px; cursor: grab;">drag_handle</span>
                                        <?= Html::a('<span class="material-icons" style="font-size: 18px;">edit</span>', ['update-main-menu', 'id' => $item->id], ['title' => 'Editar', 'class' => 'edit-link', 'style' => 'color: var(--md-sys-color-primary); text-decoration: none;']) ?>
                                        <?= Html::a('<span class="material-icons" style="font-size: 18px;">delete</span>', ['delete-main-menu', 'id' => $item->id], [
                                            'title' => 'Eliminar',
                                            'data-confirm' => '¿Está seguro que desea eliminar este item?',
                                            'data-method' => 'post',
                                            'class' => 'delete-link',
                                            'style' => 'color: #d32f2f; text-decoration: none;'
                                        ]) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li style="padding: 2rem; text-align: center; color: #999; font-style: italic; background: rgba(0,0,0,0.05); border-radius: 4px;">
                                No hay items en el menú principal.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Tab 2: Menú Footer -->
            <div class="menus-tab-content" id="menus-tab-footer">
                <div style="padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; border-top-left-radius: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <p style="color: var(--md-sys-color-on-surface-variant); margin: 0;">
                            Gestione los items del menú del footer. El menú se muestra en 4 columnas. Arrastre y suelte para ordenar los items dentro de cada columna.
                        </p>
                        <?= Html::a('Nuevo Item', ['create'], ['class' => 'btn btn-primary']) ?>
                    </div>

                    <div class="footer-menu-columns" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-bottom: 2rem;">
                        <?php for ($position = 1; $position <= 4; $position++): ?>
                            <div class="footer-menu-column" data-position="<?= $position ?>" style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
                                <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--md-sys-color-primary);">
                                    Columna <?= $position ?>
                                </h3>
                                <?php if (isset($groupedItems[$position]) && is_array($groupedItems[$position]) && !empty($groupedItems[$position])): ?>
                                    <ul class="sortable-list footer-sortable" data-position="<?= $position ?>" style="list-style: none; padding: 0; margin: 0; min-height: 50px;">
                                        <?php foreach ($groupedItems[$position] as $item): ?>
                                            <li class="sortable-item" data-item-id="<?= $item->id ?>" style="margin-bottom: 0.75rem; padding: 0.75rem; background: white; border-radius: 4px; display: flex; justify-content: space-between; align-items: flex-start;">
                                                <div style="flex: 1;">
                                                    <?php
                                                    $displayLabel = $item->label;
                                                    if (empty($displayLabel) && $item->page_id) {
                                                        $page = $item->page;
                                                        $displayLabel = $page ? $page->title : 'Sin título';
                                                    }
                                                    ?>
                                                    <strong style="display: block; margin-bottom: 0.25rem;"><?= Html::encode($displayLabel ?: 'Sin etiqueta') ?></strong>
                                                    <?php if ($item->page_id && $item->page): ?>
                                                        <small style="color: #666; display: block;">Página: <?= Html::encode($item->page->title) ?></small>
                                                    <?php elseif ($item->url): ?>
                                                        <small style="color: #666; display: block;">URL: <?= Html::encode($item->url) ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($item->status == FooterMenuItem::STATUS_INACTIVE): ?>
                                                        <span style="color: #999; font-size: 0.875rem;">(Inactivo)</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem; flex-shrink: 0; align-items: center;">
                                                    <span class="drag-handle material-icons" style="color: #999; font-size: 18px; cursor: grab;">drag_handle</span>
                                                    <?= Html::a('<span class="material-icons" style="font-size: 18px;">edit</span>', ['update', 'id' => $item->id], ['title' => 'Editar', 'class' => 'edit-link', 'style' => 'color: var(--md-sys-color-primary); text-decoration: none;']) ?>
                                                    <?= Html::a('<span class="material-icons" style="font-size: 18px;">delete</span>', ['delete', 'id' => $item->id], [
                                                        'title' => 'Eliminar',
                                                        'data-confirm' => '¿Está seguro que desea eliminar este item?',
                                                        'data-method' => 'post',
                                                        'class' => 'delete-link',
                                                        'style' => 'color: #d32f2f; text-decoration: none;'
                                                    ]) ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <ul class="sortable-list footer-sortable" data-position="<?= $position ?>" style="list-style: none; padding: 0; margin: 0; min-height: 50px;">
                                        <li style="color: #999; font-style: italic; padding: 1rem; background: rgba(0,0,0,0.05); border-radius: 4px;">No hay items en esta columna</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.menus-tabs-container {
    margin-top: 1rem;
}

.menus-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 0;
    gap: 0;
}

.menus-tab {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    bottom: -2px;
    display: flex;
    align-items: center;
    opacity: 0.8;
}

.menus-tab:hover {
    opacity: 1;
}

/* Tab 1: Menú Principal - Azul */
.menus-tab-main {
    background-color: #2196F3;
    border-bottom-color: #2196F3;
}
.menus-tab-main:hover {
    background-color: #1976D2;
}

/* Tab 2: Menú Footer - Verde */
.menus-tab-footer {
    background-color: #4CAF50;
    border-bottom-color: #4CAF50;
}
.menus-tab-footer:hover {
    background-color: #388E3C;
}

.menus-tab.active {
    opacity: 1;
}

.menus-tab-content {
    display: none;
}

.menus-tab-content.active {
    display: block;
}

.sortable-list {
    min-height: 50px;
}

.sortable-item {
    transition: all 0.2s ease;
    position: relative;
}

.sortable-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.drag-handle {
    cursor: grab;
    user-select: none;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-item a {
    pointer-events: auto !important;
    z-index: 10;
    position: relative;
    cursor: pointer !important;
}

.sortable-item .edit-link,
.sortable-item .delete-link {
    pointer-events: auto !important;
    z-index: 15;
    position: relative;
    cursor: pointer !important;
}

.footer-sortable .sortable-item {
    cursor: default;
}

.footer-sortable .drag-handle {
    cursor: grab;
}

.footer-sortable .drag-handle:active {
    cursor: grabbing;
}

.sortable-item.ui-sortable-helper {
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transform: rotate(2deg);
}

.ui-sortable-placeholder {
    background: #e3f2fd !important;
    border: 2px dashed #2196F3 !important;
    visibility: visible !important;
    height: 60px !important;
    margin-bottom: 0.5rem !important;
    list-style: none !important;
}

.footer-sortable .ui-sortable-placeholder {
    background: #e3f2fd !important;
    border: 2px dashed #4CAF50 !important;
    visibility: visible !important;
    height: 60px !important;
    margin-bottom: 0.5rem !important;
    list-style: none !important;
}

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

<script>
// Function to initialize footer menu sortable
function initFooterSortable() {
        // Destroy any existing sortable instances
        $('.footer-sortable').sortable('destroy');
        
        // Check if footer sortable elements exist
        if ($('.footer-sortable').length === 0) {
            console.log('No footer-sortable elements found');
            return;
        }
        
        console.log('Initializing footer sortable...');
        
        // Initialize sortable for footer menu columns
        $('.footer-sortable').sortable({
            connectWith: '.footer-sortable',
            handle: '.drag-handle',
            cancel: 'a,button',
            distance: 10,
            tolerance: 'pointer',
            placeholder: 'ui-sortable-placeholder',
            cursor: 'move',
            opacity: 0.8,
            forcePlaceholderSize: true,
            update: function(event, ui) {
                console.log('Footer sortable update triggered');
                var orders = {};
                
                $('.footer-menu-column').each(function() {
                    var position = $(this).data('position');
                    var itemOrders = [];
                    
                    $(this).find('.sortable-item').each(function() {
                        itemOrders.push($(this).data('item-id'));
                    });
                    
                    if (itemOrders.length > 0) {
                        orders[position] = itemOrders;
                    }
                });
                
                console.log('Sending order update:', orders);
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['update-footer-order']) ?>',
                    method: 'POST',
                    data: {
                        orders: orders,
                        <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
                    },
                    success: function(response) {
                        console.log('Footer order update response:', response);
                        if (response.success) {
                            console.log('Orden del menú footer actualizado');
                        } else {
                            alert('Error: ' + (response.message || 'Error desconocido'));
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Footer order update error:', error, xhr);
                        alert('Error al actualizar el orden. Recargando página...');
                        location.reload();
                    }
                });
            }
        });
        
    console.log('Footer sortable initialized');
}

// Function to switch between menu tabs
function switchMenusTab(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Hide all tab contents
    var allContents = document.querySelectorAll('.menus-tab-content');
    for (var i = 0; i < allContents.length; i++) {
        allContents[i].classList.remove('active');
    }
    
    // Remove active class from all tabs
    var allTabs = document.querySelectorAll('.menus-tab');
    for (var i = 0; i < allTabs.length; i++) {
        allTabs[i].classList.remove('active');
    }
    
    // Show selected tab content
    var targetContentId = 'menus-tab-' + tabName;
    var targetContent = document.getElementById(targetContentId);
    console.log('Target content element:', targetContentId, targetContent);
    if (targetContent) {
        targetContent.classList.add('active');
        console.log('Added active class to content');
    } else {
        console.error('Target content not found:', targetContentId);
    }
    
    // Add active class to selected tab
    var targetTab = document.querySelector('.menus-tab[data-tab="' + tabName + '"]');
    console.log('Target tab element:', targetTab);
    if (targetTab) {
        targetTab.classList.add('active');
        console.log('Added active class to tab');
    } else {
        console.error('Target tab not found for data-tab:', tabName);
    }
    
    // Initialize footer sortable if footer tab is activated
    if (tabName === 'footer') {
        // Small delay to ensure DOM is ready
        setTimeout(function() {
            initFooterSortable();
        }, 100);
    }
}

// Make functions available globally
window.switchMenusTab = switchMenusTab;
window.initFooterSortable = initFooterSortable;

// Initialize sortable for main menu
$(document).ready(function() {
    $('#main-menu-sortable').sortable({
        handle: '.drag-handle',
        cancel: 'a',
        distance: 5,
        placeholder: 'ui-sortable-placeholder',
        update: function(event, ui) {
            var itemIds = [];
            $('#main-menu-sortable .sortable-item').each(function() {
                itemIds.push($(this).data('item-id'));
            });
            
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['update-main-menu-order']) ?>',
                method: 'POST',
                data: {
                    itemIds: itemIds,
                    <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Orden del menú principal actualizado');
                    } else {
                        alert('Error: ' + (response.message || 'Error desconocido'));
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error al actualizar el orden. Recargando página...');
                    location.reload();
                }
            });
        }
    });
    
    // Footer sortable will be initialized when footer tab is activated
    // via switchMenusTab function
});
</script>
