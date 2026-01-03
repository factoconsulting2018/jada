<?php

/** @var yii\web\View $this */
/** @var app\models\Page $model */

use yii\helpers\Html;

$this->title = $model->title;
?>
<div class="page-view">
    <div class="container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem; box-sizing: border-box; width: 100%;">
        <article class="page-content" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 100%; box-sizing: border-box; overflow: hidden;">
            <h1 style="font-size: 2.5rem; font-weight: 400; margin-bottom: 1.5rem; color: var(--md-sys-color-primary); word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;">
                <?= Html::encode($model->title) ?>
            </h1>
            
            <div style="line-height: 1.8; color: var(--md-sys-color-on-surface); word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; overflow: hidden;">
                <?= nl2br(Html::encode($model->content)) ?>
            </div>
        </article>
    </div>
</div>

