<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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
                    'change-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex($tab = 'pending')
    {
        $searchModel = new ClientSearch();
        $params = Yii::$app->request->queryParams;
        
        // Set status based on tab
        $statusMap = [
            'pending' => Client::STATUS_PENDING,
            'accepted' => Client::STATUS_ACCEPTED,
            'rejected' => Client::STATUS_REJECTED,
        ];
        
        $status = $statusMap[$tab] ?? Client::STATUS_PENDING;
        $searchModel->status = $status;
        
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['status' => $status]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentTab' => $tab,
        ]);
    }

    /**
     * Displays a single Client model.
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cliente creado exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cliente actualizado exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Changes client status
     * @param integer $id
     * @param integer $status
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionChangeStatus($id, $status)
    {
        $model = $this->findModel($id);
        $model->status = $status;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Estado del cliente actualizado exitosamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al actualizar el estado del cliente.');
        }
        
        // Redirect to appropriate tab
        $tabMap = [
            Client::STATUS_PENDING => 'pending',
            Client::STATUS_ACCEPTED => 'accepted',
            Client::STATUS_REJECTED => 'rejected',
        ];
        $tab = $tabMap[$status] ?? 'pending';
        
        return $this->redirect(['index', 'tab' => $tab]);
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $currentStatus = $model->status;
        $model->delete();
        Yii::$app->session->setFlash('success', 'Cliente eliminado exitosamente.');
        
        // Redirect to appropriate tab
        $tabMap = [
            Client::STATUS_PENDING => 'pending',
            Client::STATUS_ACCEPTED => 'accepted',
            Client::STATUS_REJECTED => 'rejected',
        ];
        $tab = $tabMap[$currentStatus] ?? 'pending';

        return $this->redirect(['index', 'tab' => $tab]);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}
