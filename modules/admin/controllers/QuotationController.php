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
        $searchModel = new QuotationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        
        // Delete product image if exists
        if ($model->product_image && file_exists(Yii::getAlias('@webroot') . $model->product_image)) {
            unlink(Yii::getAlias('@webroot') . $model->product_image);
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Cotización eliminada exitosamente.');

        return $this->redirect(['index']);
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
                ->setSubject('Cotización - ' . $product->name)
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

