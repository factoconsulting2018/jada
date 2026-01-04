<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
use app\models\ProductTechnicalSpec;
use app\models\ProductVideo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use ZipArchive;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Configure pagination to show 10 items per page
        $dataProvider->pagination = [
            'pageSize' => 10,
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            $uploadSuccess = true;
            if ($model->imageFiles) {
                $uploadSuccess = $model->upload();
            }
            
            if ($uploadSuccess) {
                if ($model->save(false)) {
                    // Save related products
                    $this->saveRelatedProducts($model);
                    // Save technical specs
                    $this->saveTechnicalSpecs($model);
                    // Save videos
                    $this->saveProductVideos($model);
                    
                    Yii::$app->session->setFlash('success', 'Producto creado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $categories = Category::find()->where(['status' => Category::STATUS_ACTIVE])->all();
        $categoryList = \yii\helpers\ArrayHelper::map($categories, 'id', 'name');

        $brands = \app\models\Brand::find()->where(['status' => \app\models\Brand::STATUS_ACTIVE])->all();
        $brandList = \yii\helpers\ArrayHelper::map($brands, 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'categoryList' => $categoryList,
            'brandList' => $brandList,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            // Upload images if provided
            $uploadSuccess = true;
            if ($model->imageFiles) {
                $uploadSuccess = $model->upload();
            }
            
            if ($uploadSuccess) {
                if ($model->save(false)) {
                    // Save related products
                    $this->saveRelatedProducts($model);
                    // Save technical specs
                    $this->saveTechnicalSpecs($model);
                    // Save videos
                    $this->saveProductVideos($model);
                    
                    Yii::$app->session->setFlash('success', 'Producto actualizado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $categories = Category::find()->where(['status' => Category::STATUS_ACTIVE])->all();
        $categoryList = \yii\helpers\ArrayHelper::map($categories, 'id', 'name');
        
        $brands = \app\models\Brand::find()->where(['status' => \app\models\Brand::STATUS_ACTIVE])->all();
        $brandList = \yii\helpers\ArrayHelper::map($brands, 'id', 'name');

        return $this->render('update', [
            'model' => $model,
            'categoryList' => $categoryList,
            'brandList' => $brandList,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Search products for autocomplete
     * @return Response
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        $excludeId = Yii::$app->request->get('exclude_id');
        
        if (strlen($query) < 2) {
            return [];
        }
        
        $products = Product::find()
            ->where(['like', 'name', $query])
            ->andWhere(['status' => Product::STATUS_ACTIVE])
            ->limit(10);
        
        if ($excludeId) {
            $products->andWhere(['!=', 'id', $excludeId]);
        }
        
        $results = [];
        foreach ($products->all() as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->formattedPrice,
            ];
        }
        
        return $results;
    }

    /**
     * Generate QR code for product
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionQr($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('qr', [
            'model' => $model,
        ]);
    }

    /**
     * Generate PDF for product
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        
        $relatedProducts = $model->getRelatedProducts()->all();
        
        $productUrl = \yii\helpers\Url::to(['/product/view', 'id' => $model->id], true);
        
        // Generate QR Code
        $qrCode = null;
        try {
            $qrCode = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($productUrl)
                ->size(200)
                ->margin(10)
                ->errorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                ->build();
        } catch (\Exception $e) {
            Yii::error('Error generating QR code: ' . $e->getMessage());
        }
        
        $html = $this->renderPartial('_pdf', [
            'model' => $model,
            'relatedProducts' => $relatedProducts,
            'qrCode' => $qrCode,
        ]);
        
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
            'format' => \kartik\mpdf\Pdf::FORMAT_LETTER,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $html,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .product-section { margin-bottom: 20px; }
                .product-info { margin: 10px 0; }
                .product-image { max-width: 300px; margin: 10px 0; }
                .related-products { margin-top: 30px; }
                .product-card { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .contact-info { background-color: #4caf50; color: white; padding: 20px; margin-top: 30px; page-break-before: auto; }
                .contact-info h3 { margin-top: 0; }
            ',
            'options' => [
                'title' => $model->name,
                'subject' => 'Información del Producto',
            ],
            'methods' => [
                'SetHeader' => [$model->name],
                'SetFooter' => ['{PAGENO}'],
            ],
        ]);
        
        return $pdf->render();
    }

    /**
     * Download backup (ZIP file) with product information and images
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBackup($id)
    {
        $model = $this->findModel($id);
        
        // Create temporary directory for ZIP
        $tempDir = Yii::getAlias('@runtime/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        // Clean product name for filename (remove invalid characters)
        $productName = $model->name;
        $productName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $productName); // Remove special characters
        $productName = preg_replace('/\s+/', '_', trim($productName)); // Replace spaces with underscores
        $productName = substr($productName, 0, 100); // Limit length
        
        $zipFileName = 'backup_' . $productName . '_' . date('Y-m-d_His') . '.zip';
        $zipPath = $tempDir . '/' . $zipFileName;
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            Yii::$app->session->setFlash('error', 'No se pudo crear el archivo ZIP.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        // Generate text file with product information
        $infoText = $this->generateProductInfoText($model);
        $zip->addFromString('informacion_producto.txt', $infoText);
        
        // Add images to a folder
        $allImages = $model->getAllImages();
        if (!empty($allImages)) {
            $imagesDir = 'imagenes';
            foreach ($allImages as $imagePath) {
                $fullImagePath = Yii::getAlias('@webroot') . $imagePath;
                if (file_exists($fullImagePath)) {
                    $imageName = basename($imagePath);
                    $zip->addFile($fullImagePath, $imagesDir . '/' . $imageName);
                }
            }
        }
        
        // Add technical specs documents to a folder
        $technicalSpecs = $model->getTechnicalSpecs()->all();
        if (!empty($technicalSpecs)) {
            $docsDir = 'documentos_tecnicos';
            foreach ($technicalSpecs as $spec) {
                if ($spec->file_path) {
                    $fullDocPath = Yii::getAlias('@webroot') . $spec->file_path;
                    if (file_exists($fullDocPath)) {
                        $docName = $spec->name ? $spec->name . '.pdf' : basename($spec->file_path);
                        // Clean filename to avoid issues with special characters
                        $docName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $docName);
                        $zip->addFile($fullDocPath, $docsDir . '/' . $docName);
                    }
                }
            }
        }
        
        $zip->close();
        
        // Send file to browser
        if (file_exists($zipPath)) {
            Yii::$app->response->sendFile($zipPath, $zipFileName, [
                'mimeType' => 'application/zip',
                'inline' => false,
            ])->send();
            
            // Delete temporary file after sending
            unlink($zipPath);
            Yii::$app->end();
        } else {
            Yii::$app->session->setFlash('error', 'Error al generar el archivo de backup.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * Generate text file content with product information
     * @param Product $model
     * @return string
     */
    protected function generateProductInfoText($model)
    {
        $text = "BACKUP DE PRODUCTO\n";
        $text .= str_repeat("=", 50) . "\n\n";
        
        $text .= "INFORMACIÓN BÁSICA\n";
        $text .= str_repeat("-", 50) . "\n";
        $text .= "ID: " . $model->id . "\n";
        $text .= "Nombre: " . $model->name . "\n";
        $text .= "Descripción: " . ($model->description ?: 'Sin descripción') . "\n";
        $text .= "Precio: ₡" . number_format($model->price, 2, '.', ',') . "\n";
        $text .= "Estado: " . ($model->status == Product::STATUS_ACTIVE ? 'Activo' : 'Inactivo') . "\n\n";
        
        $text .= "CATEGORÍA\n";
        $text .= str_repeat("-", 50) . "\n";
        $text .= "Categoría: " . ($model->category ? $model->category->name : 'Sin categoría') . "\n\n";
        
        $text .= "MARCA\n";
        $text .= str_repeat("-", 50) . "\n";
        $text .= "Marca: " . ($model->brand ? $model->brand->name : 'Sin marca') . "\n\n";
        
        // Technical Specs
        $technicalSpecs = $model->getTechnicalSpecs()->all();
        if (!empty($technicalSpecs)) {
            $text .= "ESPECIFICACIONES TÉCNICAS\n";
            $text .= str_repeat("-", 50) . "\n";
            foreach ($technicalSpecs as $spec) {
                $text .= "- " . ($spec->name ?: 'Sin nombre') . "\n";
            }
            $text .= "\n";
        }
        
        // Videos
        $videos = $model->getVideos()->all();
        if (!empty($videos)) {
            $text .= "VÍDEOS\n";
            $text .= str_repeat("-", 50) . "\n";
            foreach ($videos as $video) {
                $text .= "- " . ($video->name ?: 'Sin nombre') . ": " . $video->video_url . "\n";
            }
            $text .= "\n";
        }
        
        // Related Products
        $relatedProducts = $model->getRelatedProducts()->all();
        if (!empty($relatedProducts)) {
            $text .= "PRODUCTOS RELACIONADOS\n";
            $text .= str_repeat("-", 50) . "\n";
            foreach ($relatedProducts as $related) {
                $text .= "- " . $related->name . " (ID: " . $related->id . ")\n";
            }
            $text .= "\n";
        }
        
        // QR Labels
        if ($model->qr_label_top || $model->qr_label_bottom) {
            $text .= "ETIQUETAS QR\n";
            $text .= str_repeat("-", 50) . "\n";
            if ($model->qr_label_top) {
                $text .= "Etiqueta Superior: " . $model->qr_label_top . "\n";
            }
            if ($model->qr_label_bottom) {
                $text .= "Etiqueta Inferior: " . $model->qr_label_bottom . "\n";
            }
            $text .= "\n";
        }
        
        // Images
        $allImages = $model->getAllImages();
        if (!empty($allImages)) {
            $text .= "IMÁGENES\n";
            $text .= str_repeat("-", 50) . "\n";
            $text .= "Total de imágenes: " . count($allImages) . "\n";
            foreach ($allImages as $index => $imagePath) {
                $text .= ($index + 1) . ". " . basename($imagePath) . "\n";
            }
            $text .= "\n";
        }
        
        $text .= "FECHAS\n";
        $text .= str_repeat("-", 50) . "\n";
        $text .= "Fecha de Creación: " . date('Y-m-d H:i:s', $model->created_at) . "\n";
        $text .= "Fecha de Actualización: " . date('Y-m-d H:i:s', $model->updated_at) . "\n\n";
        
        $text .= "Backup generado el: " . date('Y-m-d H:i:s') . "\n";
        
        return $text;
    }

    /**
     * Save related products
     */
    protected function saveRelatedProducts($model)
    {
        $relatedIds = Yii::$app->request->post('related_products', []);
        
        // Delete existing relationships
        Yii::$app->db->createCommand()
            ->delete('{{%product_related}}', ['product_id' => $model->id])
            ->execute();
        
        // Insert new relationships
        if (!empty($relatedIds)) {
            foreach ($relatedIds as $relatedId) {
                Yii::$app->db->createCommand()
                    ->insert('{{%product_related}}', [
                        'product_id' => $model->id,
                        'related_product_id' => $relatedId,
                    ])
                    ->execute();
            }
        }
    }

    /**
     * Save technical specs
     */
    protected function saveTechnicalSpecs($model)
    {
        // Handle existing specs updates and deletions
        $existingSpecs = Yii::$app->request->post('technical_specs', []);
        if ($existingSpecs) {
            foreach ($existingSpecs as $specId => $specData) {
                if (isset($specData['delete']) && $specData['delete']) {
                    // Delete spec
                    ProductTechnicalSpec::findOne($specId)?->delete();
                } else {
                    // Update spec name
                    $spec = ProductTechnicalSpec::findOne($specId);
                    if ($spec) {
                        $spec->name = $specData['name'] ?? null;
                        $spec->save(false);
                    }
                }
            }
        }
        
        // Handle new specs
        $newSpecs = Yii::$app->request->post('technical_specs_new', []);
        if ($newSpecs) {
            foreach ($newSpecs as $newSpecData) {
                $spec = new ProductTechnicalSpec();
                $spec->product_id = $model->id;
                $spec->name = $newSpecData['name'] ?? null;
                
                $specFile = UploadedFile::getInstanceByName('technical_specs_new[' . array_search($newSpecData, $newSpecs) . '][file]');
                if ($specFile) {
                    $path = Yii::getAlias('@webroot/uploads/technical-specs/');
                    \yii\helpers\FileHelper::createDirectory($path);
                    
                    $fileName = uniqid() . '_' . time() . '.' . $specFile->extension;
                    $filePath = $path . $fileName;
                    
                    if ($specFile->saveAs($filePath)) {
                        $spec->file_path = '/uploads/technical-specs/' . $fileName;
                        $spec->save(false);
                    }
                }
            }
        }
    }

    /**
     * Save product videos
     */
    protected function saveProductVideos($model)
    {
        // Handle existing videos updates and deletions
        $existingVideos = Yii::$app->request->post('product_videos', []);
        if ($existingVideos) {
            foreach ($existingVideos as $videoId => $videoData) {
                if (isset($videoData['delete']) && $videoData['delete']) {
                    // Delete video
                    ProductVideo::findOne($videoId)?->delete();
                } else {
                    // Update video
                    $video = ProductVideo::findOne($videoId);
                    if ($video) {
                        $video->name = $videoData['name'] ?? null;
                        $video->video_url = $videoData['video_url'] ?? null;
                        $video->save(false);
                    }
                }
            }
        }
        
        // Handle new videos
        $newVideos = Yii::$app->request->post('product_videos_new', []);
        if ($newVideos) {
            foreach ($newVideos as $newVideoData) {
                $video = new ProductVideo();
                $video->product_id = $model->id;
                $video->name = $newVideoData['name'] ?? null;
                $video->video_url = $newVideoData['video_url'] ?? null;
                $video->save(false);
            }
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
