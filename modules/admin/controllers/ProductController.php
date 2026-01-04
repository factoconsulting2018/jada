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

        return $this->render('create', [
            'model' => $model,
            'categoryList' => $categoryList,
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

        return $this->render('update', [
            'model' => $model,
            'categoryList' => $categoryList,
        ]);
    }

    /**
     * Search products for autocomplete
     * @return Response
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        $excludeId = Yii::$app->request->get('exclude_id', 0);
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [];
        }
        
        $products = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->andWhere(['like', 'name', $query]);
        
        if ($excludeId) {
            $products->andWhere(['!=', 'id', $excludeId]);
        }
        
        $products = $products->limit(10)->all();
        
        $results = [];
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->formattedPrice,
            ];
        }
        
        return $results;
    }

    /**
     * Save related products
     */
    protected function saveRelatedProducts($model)
    {
        $relatedIds = Yii::$app->request->post('related_products', []);
        
        // Delete existing relations
        Yii::$app->db->createCommand()
            ->delete('{{%product_related}}', ['product_id' => $model->id])
            ->execute();
        
        // Insert new relations
        if (!empty($relatedIds) && is_array($relatedIds)) {
            foreach ($relatedIds as $relatedId) {
                $relatedId = (int)$relatedId;
                if ($relatedId && $relatedId != $model->id) {
                    Yii::$app->db->createCommand()
                        ->insert('{{%product_related}}', [
                            'product_id' => $model->id,
                            'related_product_id' => $relatedId,
                            'created_at' => time(),
                        ])
                        ->execute();
                }
            }
        }
    }

    /**
     * Save technical specs
     */
    protected function saveTechnicalSpecs($model)
    {
        $post = Yii::$app->request->post();
        
        // Update existing specs
        if (isset($post['technical_specs']) && is_array($post['technical_specs'])) {
            foreach ($post['technical_specs'] as $specId => $specData) {
                if (isset($specData['delete']) && $specData['delete'] == '1') {
                    // Delete spec
                    $spec = ProductTechnicalSpec::findOne($specId);
                    if ($spec) {
                        // Delete file
                        if ($spec->file_path && file_exists(Yii::getAlias('@webroot') . $spec->file_path)) {
                            @unlink(Yii::getAlias('@webroot') . $spec->file_path);
                        }
                        $spec->delete();
                    }
                } elseif (isset($specData['id'])) {
                    // Update existing spec
                    $spec = ProductTechnicalSpec::findOne($specData['id']);
                    if ($spec) {
                        $spec->name = !empty($specData['name']) ? $specData['name'] : null;
                        $spec->save(false);
                    }
                }
            }
        }
        
        // Add new specs
        if (isset($post['technical_specs_new']) && is_array($post['technical_specs_new'])) {
            foreach ($post['technical_specs_new'] as $index => $newSpecData) {
                $file = UploadedFile::getInstanceByName('technical_specs_new[' . $index . '][file]');
                if ($file) {
                    $spec = new ProductTechnicalSpec();
                    $spec->product_id = $model->id;
                    $spec->file = $file;
                    $spec->name = !empty($newSpecData['name']) ? $newSpecData['name'] : null;
                    
                    if ($spec->upload()) {
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
        $post = Yii::$app->request->post();
        
        // Migrate old video_url to new table if exists
        if (!$model->isNewRecord && $model->video_url) {
            $existingVideos = ProductVideo::find()
                ->where(['product_id' => $model->id])
                ->count();
            if ($existingVideos == 0) {
                $oldVideo = new ProductVideo();
                $oldVideo->product_id = $model->id;
                $oldVideo->video_url = $model->video_url;
                $oldVideo->name = null;
                $oldVideo->save(false);
            }
        }
        
        // Update existing videos
        if (isset($post['product_videos']) && is_array($post['product_videos'])) {
            foreach ($post['product_videos'] as $videoId => $videoData) {
                if (isset($videoData['delete']) && $videoData['delete'] == '1') {
                    // Delete video
                    $video = ProductVideo::findOne($videoId);
                    if ($video) {
                        $video->delete();
                    }
                } elseif (isset($videoData['id'])) {
                    // Update existing video
                    $video = ProductVideo::findOne($videoData['id']);
                    if ($video) {
                        $video->name = !empty($videoData['name']) ? $videoData['name'] : null;
                        $video->video_url = $videoData['video_url'] ?? $video->video_url;
                        $video->save(false);
                    }
                }
            }
        }
        
        // Add new videos
        if (isset($post['product_videos_new']) && is_array($post['product_videos_new'])) {
            foreach ($post['product_videos_new'] as $index => $newVideoData) {
                if (!empty($newVideoData['video_url'])) {
                    $video = new ProductVideo();
                    $video->product_id = $model->id;
                    $video->video_url = $newVideoData['video_url'];
                    $video->name = !empty($newVideoData['name']) ? $newVideoData['name'] : null;
                    
                    if ($video->save(false)) {
                        // Video saved successfully
                    }
                }
            }
        }
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
        Yii::$app->session->setFlash('success', 'Producto eliminado exitosamente.');

        return $this->redirect(['index']);
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

