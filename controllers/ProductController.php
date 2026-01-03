<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Url;

/**
 * ProductController implements the frontend actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * Lists all Product models (catalog).
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => Product::STATUS_ACTIVE]);
        
        $categories = Category::getMainCategories();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status != Product::STATUS_ACTIVE) {
            throw new NotFoundHttpException('El producto solicitado no está disponible.');
        }

        // Related products - first try manually related, then by category
        $relatedProducts = $model->relatedProducts;
        if (empty($relatedProducts)) {
            $relatedProducts = Product::find()
                ->where(['category_id' => $model->category_id])
                ->andWhere(['status' => Product::STATUS_ACTIVE])
                ->andWhere(['!=', 'id', $model->id])
                ->limit(4)
                ->all();
        } else {
            // Limit to 4 if manually related
            $relatedProducts = array_slice($relatedProducts, 0, 4);
        }

        return $this->render('view', [
            'model' => $model,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Request quotation for a product (adds to cart and redirects to quotation page)
     * @param integer $id
     * @return mixed
     */
    public function actionRequestQuote($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $product = $this->findModel($id);
        
        if ($product->status != Product::STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'El producto no está disponible.'];
        }

        // Add product to quotation cart
        $cart = Yii::$app->session->get('quotation_cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id] += 1;
        } else {
            $cart[$product->id] = 1;
        }
        
        Yii::$app->session->set('quotation_cart', $cart);
        
        return [
            'success' => true, 
            'message' => 'Producto agregado al carrito de cotización.',
            'redirect' => Url::to(['/quotation'])
        ];
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}

