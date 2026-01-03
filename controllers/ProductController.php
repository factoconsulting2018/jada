<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
use app\models\Quotation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\FileHelper;
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
        
        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->all();

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
     * Request quotation for a product
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

        $quotation = new Quotation();
        
        if (Yii::$app->request->isPost && $quotation->load(Yii::$app->request->post())) {
            $quotation->product_id = $product->id;
            
            // Copy product image
            $allImages = $product->getAllImages();
            if (!empty($allImages)) {
                $imagePath = $allImages[0];
                $productImagePath = Yii::getAlias('@webroot') . $imagePath;
                $quotationDir = Yii::getAlias('@webroot/uploads/quotations/');
                FileHelper::createDirectory($quotationDir);
                
                $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                if (empty($extension)) {
                    $extension = 'jpg';
                }
                $fileName = 'product_' . $product->id . '_' . time() . '.' . $extension;
                $destPath = $quotationDir . $fileName;
                
                if (file_exists($productImagePath) && copy($productImagePath, $destPath)) {
                    $quotation->product_image = '/uploads/quotations/' . $fileName;
                }
            }
            
            if ($quotation->save()) {
                // Send email
                $this->sendQuotationEmail($quotation);
                
                return ['success' => true, 'message' => 'Su solicitud de cotización ha sido enviada exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al procesar la solicitud.', 'errors' => $quotation->errors];
            }
        }
        
        return ['success' => false, 'message' => 'Solicitud inválida.'];
    }

    /**
     * Send quotation email
     */
    protected function sendQuotationEmail($quotation)
    {
        try {
            $product = $quotation->product;
            $adminEmail = Yii::$app->params['adminEmail'];
            
            $htmlBody = Yii::$app->view->renderFile('@app/views/mail/quotation.php', [
                'quotation' => $quotation,
                'product' => $product,
            ]);
            
            $message = Yii::$app->mailer->compose()
                ->setTo($adminEmail)
                ->setFrom([Yii::$app->params['supportEmail'] => 'Tienda Online'])
                ->setSubject('Nueva Solicitud de Cotización - ' . $product->name)
                ->setHtmlBody($htmlBody);
            
            if ($quotation->product_image && file_exists(Yii::getAlias('@webroot') . $quotation->product_image)) {
                $message->attach(Yii::getAlias('@webroot') . $quotation->product_image);
            }
            
            $message->send();
        } catch (\Exception $e) {
            Yii::error('Error sending quotation email: ' . $e->getMessage());
        }
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

