<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use app\models\Product;
use app\models\Category;
use app\models\Banner;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $banners = Banner::getActiveBanners();
        $featuredProducts = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(8)
            ->all();
        
        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->limit(6)
            ->all();

        return $this->render('index', [
            'banners' => $banners,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }

    /**
     * Search products for autocomplete
     * 
     * @return Response
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [];
        }
        
        $products = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->andWhere(['like', 'name', $query])
            ->limit(10)
            ->all();
        
        $results = [];
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->formattedPrice,
                'url' => \yii\helpers\Url::to(['/product/view', 'id' => $product->id]),
            ];
        }
        
        return $results;
    }

    /**
     * Filter products by category (AJAX)
     * 
     * @return Response
     */
    public function actionFilterProducts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $categoryId = Yii::$app->request->get('category_id', null);
        
        $query = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE]);
        
        if ($categoryId && $categoryId !== 'all') {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        $products = $query->orderBy(['created_at' => SORT_DESC])->limit(20)->all();
        
        $results = [];
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->formattedPrice,
                'image' => $product->imageUrl,
                'category' => $product->category->name ?? '',
                'url' => \yii\helpers\Url::to(['/product/view', 'id' => $product->id]),
            ];
        }
        
        return ['products' => $results];
    }
}

