<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\ParallaxBackground;
use app\models\ParallaxBackgroundSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParallaxBackgroundController implements the CRUD actions for ParallaxBackground model.
 */
class ParallaxBackgroundController extends Controller
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
     * Lists all ParallaxBackground models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ParallaxBackgroundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ParallaxBackground model.
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
     * Creates a new ParallaxBackground model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ParallaxBackground();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->upload() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Fondo parallax creado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ParallaxBackground model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = \yii\web\UploadedFile::getInstance($model, 'imageFile');
            $uploadSuccess = true;
            if ($model->imageFile) {
                $uploadSuccess = $model->upload();
            }
            if ($uploadSuccess && $model->save()) {
                Yii::$app->session->setFlash('success', 'Fondo parallax actualizado exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ParallaxBackground model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete image file
        if ($model->image && file_exists(Yii::getAlias('@webroot') . $model->image)) {
            @unlink(Yii::getAlias('@webroot') . $model->image);
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Fondo parallax eliminado exitosamente.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the ParallaxBackground model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ParallaxBackground the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ParallaxBackground::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}

