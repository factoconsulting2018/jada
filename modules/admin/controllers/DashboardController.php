<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\Category;
use app\models\Client;
use app\models\Banner;

/**
 * Dashboard controller for the `admin` module
 */
class DashboardController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $stats = [
            'products' => Product::find()->count(),
            'activeProducts' => Product::find()->where(['status' => Product::STATUS_ACTIVE])->count(),
            'categories' => Category::find()->count(),
            'clients' => Client::find()->count(),
            'banners' => Banner::find()->where(['status' => Banner::STATUS_ACTIVE])->count(),
        ];

        return $this->render('index', [
            'stats' => $stats,
        ]);
    }
}

