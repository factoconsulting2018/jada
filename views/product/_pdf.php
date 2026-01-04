<?php

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var app\models\Product[] $relatedProducts */
/** @var string $productUrl */

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\PriceHelper;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

$allImages = $model->getAllImages();
$videos = $model->videos;
$technicalSpecs = $model->technicalSpecs;
$dollarPrice = PriceHelper::formatDollars($model->price);

// Generate QR code
$qrCodeDataUri = '';
try {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($productUrl)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(200)
        ->build();
    
    $qrCodeDataUri = $result->getDataUri();
} catch (\Exception $e) {
    // QR code generation failed, continue without it
}
?>
<div style="position: relative;">
    <h1><?= Html::encode($model->name) ?></h1>
    
    <div class="section">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold; width: 30%;">Categoría:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= $model->category ? Html::encode($model->category->name) : 'N/A' ?></td>
            </tr>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Precio:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($model->formattedPrice) ?></td>
            </tr>
            <?php if ($dollarPrice): ?>
            <tr>
                <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Precio aprox en dólares:</td>
                <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($dollarPrice) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <?php if (!empty($allImages)): ?>
    <div class="section">
        <h2>Imágenes</h2>
        <?php foreach ($allImages as $image): ?>
            <?php 
            $imagePath = Yii::getAlias('@webroot') . $image;
            if (file_exists($imagePath)): 
                // Convert image to base64 data URI for mPDF
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageInfo = getimagesize($imagePath);
                $mimeType = $imageInfo['mime'];
                $imageDataUri = 'data:' . $mimeType . ';base64,' . $imageData;
            ?>
                <div style="margin: 10pt 0;">
                    <img src="<?= $imageDataUri ?>" alt="<?= Html::encode($model->name) ?>" style="max-width: 300pt; height: auto;" />
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($model->description): ?>
    <div class="section">
        <h2>Descripción</h2>
        <div style="text-align: justify; line-height: 1.6;">
            <?= nl2br(Html::encode($model->description)) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($technicalSpecs)): ?>
    <div class="section">
        <h2>Especificaciones Técnicas</h2>
        <ul style="list-style-type: none; padding: 0;">
            <?php foreach ($technicalSpecs as $spec): ?>
                <li style="margin: 5pt 0;">
                    <?= Html::encode($spec->getDisplayName()) ?>
                    (<?= Html::encode($spec->getFileUrl()) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($videos)): ?>
    <div class="section">
        <h2>Videos</h2>
        <ul style="list-style-type: none; padding: 0;">
            <?php foreach ($videos as $video): 
                $videoUrl = $video->video_url;
                $videoName = $video->getDisplayName();
            ?>
                <li style="margin: 5pt 0;">
                    <strong><?= Html::encode($videoName) ?>:</strong> <?= Html::encode($videoUrl) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($relatedProducts)): ?>
    <div class="section">
        <h2>Productos Relacionados</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Producto</td>
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Precio</td>
                    <?php if ($dollarPrice): ?>
                    <td style="padding: 5pt; border: 1px solid #ddd; font-weight: bold;">Precio (USD)</td>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatedProducts as $related): 
                    $relatedDollarPrice = PriceHelper::formatDollars($related->price);
                ?>
                <tr>
                    <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($related->name) ?></td>
                    <td style="padding: 5pt; border: 1px solid #ddd;"><?= Html::encode($related->formattedPrice) ?></td>
                    <?php if ($dollarPrice): ?>
                    <td style="padding: 5pt; border: 1px solid #ddd;"><?= $relatedDollarPrice ? Html::encode($relatedDollarPrice) : 'N/A' ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Contact Information at the end -->
    <div class="section" style="margin-top: 40pt; padding-top: 20pt; border-top: 1px solid #ddd;">
        <div style="background-color: #4caf50; color: white; padding: 15pt; border-radius: 4pt; text-align: center;">
            <div style="font-size: 10pt; margin-bottom: 8pt; line-height: 1.6;">
                Contáctenos: +506 4710-1005 | WhatsApp: +506 6060-7309 | San Ramón, Costa Rica.
            </div>
            <div style="font-size: 9pt; line-height: 1.6;">
                www.multiserviciosdeoccidente.com | ventas@multiserviciosdeoccidente.com
            </div>
        </div>
    </div>
</div>

