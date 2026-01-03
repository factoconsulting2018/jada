<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="text-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="alert alert-error">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        <p>
            <a href="<?= \yii\helpers\Url::to(['/']) ?>" class="btn btn-primary">Volver al Inicio</a>
        </p>
    </div>
</div>

