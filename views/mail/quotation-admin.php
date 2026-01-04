<?php

/** @var app\models\Quotation $quotation */

use yii\helpers\Html;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #6750A4;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .product-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .product-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .total {
            background: white;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
        }
        .info-box {
            background: white;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Cotización #<?= $quotation->id ?></h1>
        </div>
        <div class="content">
            <p>Se ha recibido una nueva solicitud de cotización:</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0;">Datos del Cliente:</h3>
                <p><strong>Nombre:</strong> <?= Html::encode($quotation->full_name) ?><br>
                <strong>Tipo de Identificación:</strong> <?= Html::encode($quotation->getIdTypeLabel()) ?><br>
                <strong>Cédula:</strong> <?= Html::encode($quotation->id_number) ?><br>
                <strong>Email:</strong> <?= Html::encode($quotation->email) ?><br>
                <strong>WhatsApp:</strong> <?= Html::encode($quotation->whatsapp) ?></p>
            </div>
            
            <h2>Productos Solicitados:</h2>
            
            <?php foreach ($quotation->quotationProducts as $qp): ?>
                <div class="product-item">
                    <?php if ($qp->product->image): ?>
                        <img src="<?= Html::encode(\yii\helpers\Url::to(['@web' . $qp->product->image], true)) ?>" alt="<?= Html::encode($qp->product->name) ?>">
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 5px 0;"><?= Html::encode($qp->product->name) ?></h3>
                        <p style="margin: 0; color: #666;">
                            Cantidad: <?= $qp->quantity ?> × <?= Html::encode($qp->product->formattedPrice) ?>
                        </p>
                    </div>
                    <div style="font-weight: bold;">
                        ₡<?= number_format($qp->getSubtotal(), 2, '.', ',') ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="total">
                <div>Total: ₡<?= number_format($quotation->getTotal(), 2, '.', ',') ?></div>
            </div>
            
            <p style="margin-top: 20px;">
                <a href="<?= Html::encode(\yii\helpers\Url::to(['/admin/quotation/view', 'id' => $quotation->id], true)) ?>" 
                   style="display: inline-block; padding: 10px 20px; background-color: #6750A4; color: white; text-decoration: none; border-radius: 4px;">
                    Ver Cotización Completa
                </a>
            </p>
        </div>
    </div>
</body>
</html>


