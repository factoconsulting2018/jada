<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
use app\models\ParallaxBackground;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\Html;

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
        $parallaxBackgrounds = ParallaxBackground::getActiveBySection('products_page');

        $this->view->params['breadcrumbs'][] = 'Productos';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
            'parallaxBackgrounds' => $parallaxBackgrounds,
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

        // Breadcrumbs
        $this->view->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['/products']];
        if ($model->category) {
            $this->view->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/category/view', 'id' => $model->category->id]];
        }
        $this->view->params['breadcrumbs'][] = $model->name;

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
     * Generate PDF for a product
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status != Product::STATUS_ACTIVE) {
            throw new NotFoundHttpException('El producto solicitado no está disponible.');
        }

        // Get related products
        $relatedProducts = $model->relatedProducts;
        if (empty($relatedProducts)) {
            $relatedProducts = Product::find()
                ->where(['category_id' => $model->category_id])
                ->andWhere(['status' => Product::STATUS_ACTIVE])
                ->andWhere(['!=', 'id', $model->id])
                ->limit(4)
                ->all();
        } else {
            $relatedProducts = array_slice($relatedProducts, 0, 4);
        }

        // Get product URL for QR code
        $productUrl = Url::to(['/product/view', 'id' => $model->id], true);

        // Generate QR code for footer
        $qrCodeDataUri = '';
        try {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($productUrl)
                ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->errorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                ->size(150)
                ->build();
            
            $qrCodeDataUri = $result->getDataUri();
        } catch (\Exception $e) {
            // QR code generation failed, continue without it
        }

        // Render PDF view
        $content = $this->renderPartial('_pdf', [
            'model' => $model,
            'relatedProducts' => $relatedProducts,
            'productUrl' => $productUrl,
        ]);

        // Setup footer HTML with QR code only (contact info will be at the end of content)
        $footerHtml = '<div style="text-align: right;">';
        if ($qrCodeDataUri) {
            $footerHtml .= '<img src="' . $qrCodeDataUri . '" alt="QR Code" style="width: 60pt; height: auto;" />';
        }
        $footerHtml .= '</div>';

        // Setup mPDF
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
            'format' => \kartik\mpdf\Pdf::FORMAT_LETTER,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                body { font-family: Arial, sans-serif; font-size: 11pt; }
                h1 { color: #333; font-size: 20pt; margin-bottom: 10pt; }
                h2 { color: #555; font-size: 14pt; margin-top: 15pt; margin-bottom: 8pt; border-bottom: 1px solid #ddd; padding-bottom: 5pt; }
                .section { margin-bottom: 15pt; }
                img { max-width: 100%; height: auto; }
                .product-image { max-width: 200px; margin: 5pt 0; }
                table { width: 100%; border-collapse: collapse; margin: 10pt 0; }
                table td { padding: 5pt; border: 1px solid #ddd; }
            ',
            'marginFooter' => 30,
            'options' => ['title' => $model->name],
            'methods' => [
                'SetHeader' => [$model->name],
                'SetFooter' => [$footerHtml],
            ]
        ]);

        return $pdf->render();
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

