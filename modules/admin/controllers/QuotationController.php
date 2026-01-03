<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Quotation;
use app\models\QuotationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * QuotationController implements the CRUD actions for Quotation model.
 */
class QuotationController extends Controller
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
     * Lists all Quotation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Quotation::find()->with(['quotationProducts', 'quotationProducts.product']);
        
        $search = Yii::$app->request->get('search', '');
        if (!empty($search)) {
            $query->andWhere([
                'or',
                ['like', 'full_name', $search],
                ['like', 'email', $search],
                ['like', 'id_number', $search],
                ['like', 'whatsapp', $search],
            ]);
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
        ]);
    }

    /**
     * Displays a single Quotation model.
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
     * Creates a new Quotation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Quotation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Send email to admin
            $this->sendQuotationEmail($model);
            Yii::$app->session->setFlash('success', 'Cotización creada exitosamente.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Quotation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Resend email if requested
            if (Yii::$app->request->post('resend_email')) {
                $this->sendQuotationEmail($model);
                Yii::$app->session->setFlash('success', 'Cotización actualizada y correo reenviado exitosamente.');
            } else {
                Yii::$app->session->setFlash('success', 'Cotización actualizada exitosamente.');
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Quotation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete related quotation products (cascade will handle this, but we do it explicitly)
        foreach ($model->quotationProducts as $qp) {
            $qp->delete();
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Cotización eliminada exitosamente.');

        return $this->redirect(['index']);
    }

    /**
     * Send quotation email to admin
     */
    protected function sendQuotationEmail($quotation)
    {
        try {
            $adminEmail = Yii::$app->params['adminEmail'];
            
            $htmlBody = Yii::$app->view->renderFile('@app/views/mail/quotation-admin.php', [
                'quotation' => $quotation,
            ]);
            
            $message = Yii::$app->mailer->compose()
                ->setTo($adminEmail)
                ->setFrom([Yii::$app->params['supportEmail'] => 'Tienda Online'])
                ->setSubject('Nueva Cotización #' . $quotation->id . ' - ' . $quotation->full_name)
                ->setHtmlBody($htmlBody);
            
            $message->send();
        } catch (\Exception $e) {
            Yii::error('Error sending quotation email to admin: ' . $e->getMessage());
        }
    }

    /**
     * Finds the Quotation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Quotation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quotation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}

