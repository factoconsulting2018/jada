<?php

namespace app\controllers;

use Yii;
use app\models\Page;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PageController handles public page views.
 */
class PageController extends Controller
{
    /**
     * Displays a single page by slug.
     * @param string $slug
     * @return mixed
     * @throws NotFoundHttpException if the page cannot be found
     */
    public function actionView($slug)
    {
        $model = Page::findOne(['slug' => $slug, 'status' => Page::STATUS_ACTIVE]);
        
        if ($model === null) {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}

