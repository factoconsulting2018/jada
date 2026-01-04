<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */

$this->title = 'Cotización #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cotizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$lastSentEmail = Yii::$app->session->get('quotation_last_email_' . $model->id, $model->email);
?>
<div class="quotation-view">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1><?= Html::encode($this->title) ?></h1>
            <div style="display: flex; gap: 0.5rem;">
                <?= Html::a('Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
                <button type="button" onclick="openSendEmailModal()" class="btn btn-info" style="background-color: #17a2b8; border-color: #17a2b8; color: white; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block;">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 4px;">email</span>
                    Enviar por Email
                </button>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar esta cotización?',
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
                    'label' => 'Tipo de Identificación',
                    'value' => function ($model) {
                        return $model->getIdTypeLabel();
                    },
                ],
                'id_number',
                'full_name',
                'email:email',
                'whatsapp',
                [
                    'attribute' => 'status',
                    'label' => 'Estado',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    },
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

        <h2 style="margin-top: 2rem; margin-bottom: 1rem;">Productos Cotizados</h2>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e0e0e0;">
                        <th style="padding: 0.75rem; text-align: left;">Producto</th>
                        <th style="padding: 0.75rem; text-align: center;">Cantidad</th>
                        <th style="padding: 0.75rem; text-align: right;">Precio Unitario</th>
                        <th style="padding: 0.75rem; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->quotationProducts as $qp): ?>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 0.75rem;">
                                <?= Html::a(Html::encode($qp->product->name), ['/admin/product/view', 'id' => $qp->product_id], ['target' => '_blank']) ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;"><?= $qp->quantity ?></td>
                            <td style="padding: 0.75rem; text-align: right;">₡<?= number_format($qp->price, 2, '.', ',') ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 500;">₡<?= number_format($qp->getSubtotal(), 2, '.', ',') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid #e0e0e0;">
                        <td colspan="3" style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.125rem;">Total:</td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 500; font-size: 1.25rem; color: var(--md-sys-color-primary);">
                            ₡<?= number_format($model->getTotal(), 2, '.', ',') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal para enviar por email -->
<div id="sendEmailModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div class="modal-header" style="padding: 1.5rem 2rem; background-color: #17a2b8; color: white; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 1.5rem;">Enviar Cotización por Email</h2>
            <span class="close" onclick="closeSendEmailModal()" style="color: white; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 20px;">&times;</span>
        </div>
        <form id="sendEmailForm" onsubmit="sendQuotationEmail(event, <?= $model->id ?>)">
            <div style="padding: 2rem;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="emailTo" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--md-sys-color-on-surface);">Correo Electrónico *</label>
                    <input type="email" 
                           id="emailTo" 
                           name="email" 
                           required 
                           class="form-control" 
                           value="<?= Html::encode($lastSentEmail) ?>"
                           placeholder="correo@ejemplo.com"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; box-sizing: border-box;">
                    <small style="display: block; margin-top: 0.5rem; color: #666;">
                        Se enviará un PDF con toda la información de la cotización.
                    </small>
                </div>
                <div id="emailFormMessage" style="margin: 1rem 0; padding: 1rem; border-radius: 4px; display: none;"></div>
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

<script>
function openSendEmailModal() {
    document.getElementById('sendEmailModal').style.display = 'block';
    var emailInput = document.getElementById('emailTo');
    emailInput.focus();
    emailInput.select();
}

function closeSendEmailModal() {
    document.getElementById('sendEmailModal').style.display = 'none';
    document.getElementById('emailFormMessage').style.display = 'none';
}

function sendQuotationEmail(event, quotationId) {
    event.preventDefault();
    
    var emailInput = document.getElementById('emailTo');
    var email = emailInput.value.trim();
    var messageDiv = document.getElementById('emailFormMessage');
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
    var csrfToken = document.querySelector('meta[name=csrf-token]').content;
    
    fetch('<?= \yii\helpers\Url::to(["send-email", "id" => $model->id]) ?>', {
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
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
            messageDiv.textContent = data.message || 'Cotización enviada exitosamente.';
            
            // Close modal after 2 seconds
            setTimeout(function() {
                closeSendEmailModal();
                window.location.reload();
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
