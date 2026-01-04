<?php

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var string $productUrl */

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use yii\helpers\Html;

$this->title = 'Código QR - ' . $model->name;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= Html::encode($this->title) ?></title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .qr-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .qr-content {
            text-align: center;
        }
        .qr-label-top {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        .qr-code {
            display: inline-block;
            padding: 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-label-bottom {
            font-size: 16px;
            margin-top: 20px;
            color: #666;
        }
        .print-button {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #6750A4;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }
        .btn:hover {
            background: #5a4290;
        }
        .btn-secondary {
            background: #6c757d;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="qr-content">
            <?php if ($model->qr_label_top): ?>
                <div class="qr-label-top">
                    <?= Html::encode($model->qr_label_top) ?>
                </div>
            <?php endif; ?>
            
            <div class="qr-code">
                <?php
                try {
                    $result = Builder::create()
                        ->writer(new PngWriter())
                        ->data($productUrl)
                        ->encoding(new Encoding('UTF-8'))
                        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                        ->size(300)
                        ->build();
                    
                    $dataUri = $result->getDataUri();
                    echo Html::img($dataUri, ['alt' => 'QR Code', 'style' => 'max-width: 100%; height: auto;']);
                } catch (\Exception $e) {
                    echo '<p style="color: red;">Error al generar el código QR: ' . Html::encode($e->getMessage()) . '</p>';
                }
                ?>
            </div>
            
            <?php if ($model->qr_label_bottom): ?>
                <div class="qr-label-bottom">
                    <?= Html::encode($model->qr_label_bottom) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="print-button no-print">
            <button onclick="window.print()" class="btn">
                <span>🖨️</span> Imprimir
            </button>
            <a href="<?= \yii\helpers\Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-secondary">
                ← Volver al Producto
            </a>
        </div>
    </div>
</body>
</html>

