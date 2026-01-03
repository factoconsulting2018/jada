<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
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
            
            if ($model->upload() && $model->save(false)) {
                // Save related products
                $this->saveRelatedProducts($model);
                
                Yii::$app->session->setFlash('success', 'Producto creado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
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
            
            if ($uploadSuccess && $model->save(false)) {
                // Save related products
                $this->saveRelatedProducts($model);
                
                Yii::$app->session->setFlash('success', 'Producto actualizado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
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

