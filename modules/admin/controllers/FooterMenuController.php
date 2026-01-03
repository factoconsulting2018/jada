<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\FooterMenuItem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * FooterMenuController implements the CRUD actions for FooterMenuItem model.
 */
class FooterMenuController extends Controller
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
                    'update-order' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all FooterMenuItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $items = FooterMenuItem::find()
            ->orderBy(['position' => SORT_ASC, 'order' => SORT_ASC])
            ->all();

        $grouped = [];
        foreach ($items as $item) {
            if (!isset($grouped[$item->position])) {
                $grouped[$item->position] = [];
            }
            $grouped[$item->position][] = $item;
        }

        return $this->render('index', [
            'groupedItems' => $grouped,
        ]);
    }

    /**
     * Creates a new FooterMenuItem model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FooterMenuItem();
        $model->status = FooterMenuItem::STATUS_ACTIVE;
        $model->order = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item del menú creado exitosamente.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FooterMenuItem model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item del menú actualizado exitosamente.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FooterMenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Item del menú eliminado exitosamente.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the FooterMenuItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return FooterMenuItem the loaded model
     * @throws NotFoundHttpException if the model is not found
     */
    protected function findModel($id)
    {
        if (($model = FooterMenuItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El item solicitado no existe.');
    }
}

