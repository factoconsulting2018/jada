<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\FooterMenuItem;
use app\models\MainMenuItem;
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
                    'delete-main-menu' => ['POST'],
                    'update-order' => ['POST'],
                    'update-footer-order' => ['POST'],
                    'update-main-menu-order' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all FooterMenuItem models and main menu pages.
     * @return mixed
     */
    public function actionIndex()
    {
        // Footer menu items
        $items = FooterMenuItem::find()
            ->with('page')
            ->orderBy(['position' => SORT_ASC, 'order' => SORT_ASC])
            ->all();

        $grouped = [];
        foreach ($items as $item) {
            if (!isset($grouped[$item->position])) {
                $grouped[$item->position] = [];
            }
            $grouped[$item->position][] = $item;
        }

        // Main menu items
        $mainMenuItems = MainMenuItem::find()
            ->orderBy(['order' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'groupedItems' => $grouped,
            'mainMenuItems' => $mainMenuItems,
        ]);
    }

    /**
     * Updates the order of footer menu items via AJAX.
     * @return Response
     */
    public function actionUpdateFooterOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $post = Yii::$app->request->post();
        $orders = $post['orders'] ?? [];
        
        if (empty($orders)) {
            return ['success' => false, 'message' => 'No se recibieron datos de ordenamiento.'];
        }
        
        foreach ($orders as $position => $itemOrders) {
            foreach ($itemOrders as $order => $itemId) {
                $item = FooterMenuItem::findOne($itemId);
                if ($item) {
                    $item->order = (int)$order;
                    $item->position = (int)$position;
                    if (!$item->save(false)) {
                        return ['success' => false, 'message' => 'Error al guardar el orden.'];
                    }
                }
            }
        }
        
        return ['success' => true, 'message' => 'Orden actualizado exitosamente.'];
    }

    /**
     * Updates the order of main menu items via AJAX.
     * @return Response
     */
    public function actionUpdateMainMenuOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $post = Yii::$app->request->post();
        $itemIds = $post['itemIds'] ?? [];
        
        if (empty($itemIds)) {
            return ['success' => false, 'message' => 'No se recibieron datos de ordenamiento.'];
        }
        
        foreach ($itemIds as $order => $itemId) {
            $item = MainMenuItem::findOne($itemId);
            if ($item) {
                $item->order = (int)$order;
                if (!$item->save(false)) {
                    return ['success' => false, 'message' => 'Error al guardar el orden.'];
                }
            }
        }
        
        return ['success' => true, 'message' => 'Orden actualizado exitosamente.'];
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
     * Creates a new MainMenuItem model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreateMainMenu()
    {
        $model = new MainMenuItem();
        $model->status = MainMenuItem::STATUS_ACTIVE;
        $model->type = MainMenuItem::TYPE_LINK;
        $model->order = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item del menú principal creado exitosamente.');
            return $this->redirect(['index']);
        }

        $pages = \app\models\Page::find()->where(['status' => \app\models\Page::STATUS_ACTIVE])->orderBy('title')->all();
        $pageList = \yii\helpers\ArrayHelper::map($pages, 'id', 'title');

        return $this->render('create-main-menu', [
            'model' => $model,
            'pageList' => $pageList,
        ]);
    }

    /**
     * Updates an existing MainMenuItem model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateMainMenu($id)
    {
        $model = MainMenuItem::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('El item solicitado no existe.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item del menú principal actualizado exitosamente.');
            return $this->redirect(['index']);
        }

        $pages = \app\models\Page::find()->where(['status' => \app\models\Page::STATUS_ACTIVE])->orderBy('title')->all();
        $pageList = \yii\helpers\ArrayHelper::map($pages, 'id', 'title');

        return $this->render('update-main-menu', [
            'model' => $model,
            'pageList' => $pageList,
        ]);
    }

    /**
     * Deletes an existing MainMenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteMainMenu($id)
    {
        $model = MainMenuItem::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('El item solicitado no existe.');
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Item del menú principal eliminado exitosamente.');

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

