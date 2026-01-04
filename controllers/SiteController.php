<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use app\models\Product;
use app\models\Category;
use app\models\Brand;
use app\models\Banner;
use app\models\ParallaxBackground;
use app\models\SponsorBanner;
use app\helpers\PriceHelper;

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
            ->limit(5)
            ->all();
        
        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->limit(6)
            ->all();

        $parallaxBackgrounds = [
            'products' => ParallaxBackground::getActiveBySection('products'),
            'categories' => ParallaxBackground::getActiveBySection('categories'),
        ];
        
        $sponsorBanners = SponsorBanner::getActiveBanners();

        return $this->render('index', [
            'banners' => $banners,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'parallaxBackgrounds' => $parallaxBackgrounds,
            'sponsorBanners' => $sponsorBanners,
        ]);
    }

    /**
     * Search products, categories, brands and codes for autocomplete
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
        
        $results = [];
        
        // Search products by name and code
        $products = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->andWhere([
                'or',
                ['like', 'name', $query],
                ['like', 'code', $query]
            ])
            ->limit(8)
            ->all();
        
        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => $product->formattedPrice,
                'url' => \yii\helpers\Url::to(['/product/view', 'id' => $product->id]),
            ];
        }
        
        // Search categories
        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['like', 'name', $query])
            ->limit(5)
            ->all();
        
        foreach ($categories as $category) {
            $results[] = [
                'type' => 'category',
                'id' => $category->id,
                'name' => $category->name,
                'url' => \yii\helpers\Url::to(['/category/view', 'id' => $category->id]),
            ];
        }
        
        // Search brands
        $brands = Brand::find()
            ->where(['status' => Brand::STATUS_ACTIVE])
            ->andWhere(['like', 'name', $query])
            ->limit(5)
            ->all();
        
        foreach ($brands as $brand) {
            $results[] = [
                'type' => 'brand',
                'id' => $brand->id,
                'name' => $brand->name,
                'url' => \yii\helpers\Url::to(['/products', 'brand' => $brand->id]),
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
                   $dollarPrice = PriceHelper::formatDollars($product->price);
                   $results[] = [
                       'id' => $product->id,
                       'name' => $product->name,
                       'price' => $product->formattedPrice,
                       'dollarPrice' => $dollarPrice,
                       'image' => $product->imageUrl,
                       'category' => $product->category->name ?? '',
                       'url' => \yii\helpers\Url::to(['/product/view', 'id' => $product->id]),
                   ];
               }
        
        return ['products' => $results];
    }
}

