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
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="button" id="delete-selected-btn" class="btn btn-danger" style="display: none;" onclick="deleteSelectedQuotations()">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 4px;">delete</span>
                    Eliminar Seleccionadas
                </button>
                <?= Html::a('Nueva Cotización', ['create'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <form method="get" action="<?= \yii\helpers\Url::to(['index']) ?>" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1; max-width: 500px;">
                    <label for="search-input" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar</label>
                    <input type="text" 
                           id="search-input"
                           name="search" 
                           value="<?= Html::encode($search) ?>" 
                           placeholder="Buscar por nombre, email, cédula o WhatsApp..." 
                           class="form-control">
                </div>
                <div style="min-width: 180px;">
                    <label for="status-filter" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Estado</label>
                    <select id="status-filter" name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="<?= \app\models\Quotation::STATUS_PENDING ?>" <?= (isset($_GET['status']) && $_GET['status'] == \app\models\Quotation::STATUS_PENDING) ? 'selected' : '' ?>>Pendiente</option>
                        <option value="<?= \app\models\Quotation::STATUS_IN_PROCESS ?>" <?= (isset($_GET['status']) && $_GET['status'] == \app\models\Quotation::STATUS_IN_PROCESS) ? 'selected' : '' ?>>En proceso</option>
                        <option value="<?= \app\models\Quotation::STATUS_DELETED ?>" <?= (isset($_GET['status']) && $_GET['status'] == \app\models\Quotation::STATUS_DELETED) ? 'selected' : '' ?>>Eliminada</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">Buscar</button>
                </div>
                <?php if (!empty($search) || !empty($_GET['status'])): ?>
                    <div>
                        <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-secondary" style="margin-bottom: 0;">Limpiar</a>
                    </div>
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
                    'class' => 'yii\grid\CheckboxColumn',
                    'headerOptions' => ['style' => 'width: 40px;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                ],
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
                    'format' => 'raw',
                    'value' => function ($model) {
                        $label = $model->getStatusLabel();
                        $status = $model->status;
                        $style = '';
                        
                        if ($status == \app\models\Quotation::STATUS_DELETED) {
                            $style = 'background-color: #f44336; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block;';
                        } elseif ($status == \app\models\Quotation::STATUS_IN_PROCESS) {
                            $style = 'background-color: #ffc107; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block;';
                        } else {
                            $style = 'padding: 4px 8px; display: inline-block;';
                        }
                        
                        return '<span style="' . $style . '">' . \yii\helpers\Html::encode($label) . '</span>';
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
                    'template' => '{view} {update} {email} {status} {delete}',
                    'contentOptions' => ['style' => 'text-align: left;'],
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="material-icons">visibility</span>', $url, ['title' => 'Ver']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="material-icons">edit</span>', $url, ['title' => 'Editar']);
                        },
                        'email' => function ($url, $model) {
                            return Html::a('<span class="material-icons" style="color: #17a2b8;">email</span>', '#', [
                                'title' => 'Enviar por Email',
                                'onclick' => 'openSendEmailModal(' . $model->id . ', \'' . Html::encode(addslashes($model->email)) . '\'); return false;',
                                'style' => 'cursor: pointer;'
                            ]);
                        },
                        'status' => function ($url, $model) {
                            $statusOptions = [];
                            if ($model->status != \app\models\Quotation::STATUS_PENDING) {
                                $statusOptions[] = Html::a('Pendiente', ['update-status', 'id' => $model->id, 'status' => \app\models\Quotation::STATUS_PENDING], [
                                    'class' => 'btn-status',
                                    'data-method' => 'post',
                                    'title' => 'Marcar como Pendiente',
                                ]);
                            }
                            if ($model->status != \app\models\Quotation::STATUS_IN_PROCESS) {
                                $statusOptions[] = Html::a('En proceso', ['update-status', 'id' => $model->id, 'status' => \app\models\Quotation::STATUS_IN_PROCESS], [
                                    'class' => 'btn-status',
                                    'data-method' => 'post',
                                    'title' => 'Marcar como En proceso',
                                ]);
                            }
                            if ($model->status != \app\models\Quotation::STATUS_DELETED) {
                                $statusOptions[] = Html::a('Eliminada', ['update-status', 'id' => $model->id, 'status' => \app\models\Quotation::STATUS_DELETED], [
                                    'class' => 'btn-status',
                                    'data-method' => 'post',
                                    'data-confirm' => '¿Está seguro que desea marcar esta cotización como eliminada?',
                                    'title' => 'Marcar como Eliminada',
                                ]);
                            }
                            
                            if (empty($statusOptions)) {
                                return '';
                            }
                            
                            return '<div class="status-dropdown" style="position: relative; display: inline-block;">
                                <button class="btn-status-toggle" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <span class="material-icons" title="Cambiar estado">more_vert</span>
                                </button>
                                <div class="status-dropdown-content" style="display: none; position: absolute; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-radius: 4px; z-index: 1000; min-width: 150px; right: 0; top: 100%; margin-top: 5px;">
                                    ' . implode('', array_map(function($option) {
                                        return '<div style="padding: 8px 12px; border-bottom: 1px solid #eee;">' . $option . '</div>';
                                    }, $statusOptions)) . '
                                </div>
                            </div>';
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="material-icons">delete</span>', $url, [
                                'title' => 'Eliminar',
                                'data-confirm' => '¿Está seguro que desea eliminar permanentemente esta cotización?',
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
.status-dropdown {
    position: relative;
}

.status-dropdown-content a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    transition: background-color 0.2s;
}

.status-dropdown-content a:hover {
    background-color: #f5f5f5;
}

.btn-status-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: inline-flex;
    align-items: center;
}

.btn-status-toggle:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status dropdown
    document.querySelectorAll('.btn-status-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var dropdown = this.nextElementSibling;
            var isVisible = dropdown.style.display === 'block';
            
            // Close all dropdowns
            document.querySelectorAll('.status-dropdown-content').forEach(function(d) {
                d.style.display = 'none';
            });
            
            // Toggle current dropdown
            if (!isVisible) {
                dropdown.style.display = 'block';
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.status-dropdown-content').forEach(function(d) {
            d.style.display = 'none';
        });
    });
    
    // Handle checkbox selection
    var checkboxes = document.querySelectorAll('input[type="checkbox"][name="selection[]"]');
    var selectAllCheckbox = document.querySelector('input[type="checkbox"][name="selection_all"]');
    var deleteSelectedBtn = document.getElementById('delete-selected-btn');
    
    function updateDeleteButton() {
        var checkedCount = document.querySelectorAll('input[type="checkbox"][name="selection[]"]:checked').length;
        if (checkedCount > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
            deleteSelectedBtn.innerHTML = '<span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 4px;">delete</span> Eliminar Seleccionadas (' + checkedCount + ')';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateDeleteButton);
    });
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateDeleteButton();
        });
    }
    
    // Update delete button on page load
    updateDeleteButton();
});

function deleteSelectedQuotations() {
    var selectedIds = [];
    document.querySelectorAll('input[type="checkbox"][name="selection[]"]:checked').forEach(function(checkbox) {
        selectedIds.push(checkbox.value);
    });
    
    if (selectedIds.length === 0) {
        alert('Por favor, seleccione al menos una cotización para eliminar.');
        return;
    }
    
    if (!confirm('¿Está seguro que desea eliminar ' + selectedIds.length + ' cotización(es) seleccionada(s)? Esta acción no se puede deshacer.')) {
        return;
    }
    
    // Create form and submit
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(["delete-multiple"]) ?>';
    
    // Add CSRF token
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
    csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
    form.appendChild(csrfInput);
    
    // Add selected IDs
    selectedIds.forEach(function(id) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

var currentQuotationId = null;

function openSendEmailModal(quotationId, defaultEmail) {
    currentQuotationId = quotationId;
    document.getElementById('quotationId').value = quotationId;
    
    // Get last sent email from localStorage or use default
    var lastEmail = localStorage.getItem('quotation_last_email_' + quotationId) || defaultEmail || '';
    
    var emailInput = document.getElementById('emailToIndex');
    emailInput.value = lastEmail;
    
    document.getElementById('sendEmailModal').style.display = 'block';
    emailInput.focus();
    emailInput.select();
}

function closeSendEmailModal() {
    document.getElementById('sendEmailModal').style.display = 'none';
    document.getElementById('emailFormMessageIndex').style.display = 'none';
    currentQuotationId = null;
}

function sendQuotationEmailFromIndex(event) {
    event.preventDefault();
    
    var quotationId = document.getElementById('quotationId').value;
    var emailInput = document.getElementById('emailToIndex');
    var email = emailInput.value.trim();
    var messageDiv = document.getElementById('emailFormMessageIndex');
    var submitBtn = event.target.querySelector('button[type="submit"]');
    
    if (!email) {
        messageDiv.style.display = 'block';
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
        messageDiv.textContent = 'Por favor, ingrese un correo electrónico válido.';
        return;
    }
    
    // Disable submit button
    submitBtn.disabled = true;
    var originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Enviando...';
    messageDiv.style.display = 'none';
    
    // Get CSRF token
    var csrfToken = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '<?= Yii::$app->request->csrfToken ?>';
    
    fetch('<?= \yii\helpers\Url::to(["send-email"]) ?>?id=' + quotationId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': csrfToken
        },
        body: 'email=' + encodeURIComponent(email) + '&<?= Yii::$app->request->csrfParam ?>=<?= Yii::$app->request->csrfToken ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Save email to localStorage for next time
            localStorage.setItem('quotation_last_email_' + quotationId, email);
            
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
            messageDiv.textContent = data.message || 'Cotización enviada exitosamente.';
            
            // Close modal after 2 seconds and reload
            setTimeout(function() {
                closeSendEmailModal();
                location.reload();
            }, 2000);
        } else {
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.style.border = '1px solid #f5c6cb';
            messageDiv.textContent = data.message || 'Error al enviar el correo. Por favor, intente nuevamente.';
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.style.display = 'block';
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
        messageDiv.textContent = 'Error al enviar el correo. Por favor, intente nuevamente.';
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
    });
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    var modal = document.getElementById('sendEmailModal');
    if (event.target == modal) {
        closeSendEmailModal();
    }
});
</script>

<!-- Modal para enviar por email -->
<div id="sendEmailModal" class="modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div class="modal-header" style="padding: 1.5rem 2rem; background-color: #17a2b8; color: white; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 1.5rem;">Enviar Cotización por Email</h2>
            <span class="close" onclick="closeSendEmailModal()" style="color: white; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 20px;">&times;</span>
        </div>
        <form id="sendEmailForm" onsubmit="sendQuotationEmailFromIndex(event)">
            <input type="hidden" id="quotationId" name="quotationId" value="">
            <div style="padding: 2rem;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="emailToIndex" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--md-sys-color-on-surface);">Correo Electrónico *</label>
                    <input type="email" 
                           id="emailToIndex" 
                           name="email" 
                           required 
                           class="form-control" 
                           value=""
                           placeholder="correo@ejemplo.com"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; box-sizing: border-box;">
                    <small style="display: block; margin-top: 0.5rem; color: #666;">
                        Se enviará un PDF con toda la información de la cotización.
                    </small>
                </div>
                <div id="emailFormMessageIndex" style="margin: 1rem 0; padding: 1rem; border-radius: 4px; display: none;"></div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" onclick="closeSendEmailModal()" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border: none; border-radius: 4px; font-size: 1rem; font-weight: 500; cursor: pointer; background-color: #e0e0e0; color: var(--md-sys-color-on-surface);">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border: none; border-radius: 4px; font-size: 1rem; font-weight: 500; cursor: pointer; background-color: #17a2b8; color: white;">
                        <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 4px;">send</span>
                        Enviar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
