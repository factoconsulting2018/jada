<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Quotation;
use app\models\QuotationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Html;

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
                    'delete-multiple' => ['POST'],
                    'update-status' => ['POST'],
                    'send-email' => ['POST'],
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
        
        $status = Yii::$app->request->get('status', '');
        if ($status !== '') {
            $query->andWhere(['status' => (int)$status]);
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
            'selectedStatus' => $status,
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
     * Updates quotation status
     * @param integer $id
     * @param integer $status
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id, $status)
    {
        $model = $this->findModel($id);
        
        if (!in_array($status, [Quotation::STATUS_PENDING, Quotation::STATUS_IN_PROCESS, Quotation::STATUS_DELETED])) {
            Yii::$app->session->setFlash('error', 'Estado inválido.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        $model->status = $status;
        if ($model->save(false)) {
            $statusLabels = [
                Quotation::STATUS_PENDING => 'Pendiente',
                Quotation::STATUS_IN_PROCESS => 'En proceso',
                Quotation::STATUS_DELETED => 'Eliminada',
            ];
            Yii::$app->session->setFlash('success', 'Estado actualizado a: ' . $statusLabels[$status]);
        } else {
            Yii::$app->session->setFlash('error', 'Error al actualizar el estado.');
        }
        
        return $this->redirect(['index']);
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
     * Deletes multiple Quotation models.
     * @return Response
     */
    public function actionDeleteMultiple()
    {
        $ids = Yii::$app->request->post('ids', []);
        
        if (empty($ids) || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron cotizaciones para eliminar.');
            return $this->redirect(['index']);
        }
        
        $count = 0;
        foreach ($ids as $id) {
            $model = Quotation::findOne($id);
            if ($model) {
                // Delete related quotation products
                foreach ($model->quotationProducts as $qp) {
                    $qp->delete();
                }
                $model->delete();
                $count++;
            }
        }
        
        if ($count > 0) {
            Yii::$app->session->setFlash('success', $count . ' cotización(es) eliminada(s) exitosamente.');
        } else {
            Yii::$app->session->setFlash('error', 'No se pudo eliminar ninguna cotización.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Send quotation email with PDF attachment
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSendEmail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = $this->findModel($id);
        $email = Yii::$app->request->post('email', '');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Por favor, ingrese un correo electrónico válido.'
            ];
        }
        
        try {
            // Generate PDF
            $pdfContent = $this->generateQuotationPdf($model);
            
            // Save email to session for autocomplete
            Yii::$app->session->set('quotation_last_email_' . $model->id, $email);
            
            // Send email with PDF attachment
            $message = Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['supportEmail'] => 'Tienda Online'])
                ->setSubject('Cotización #' . $model->id . ' - ' . $model->full_name)
                ->setHtmlBody('Adjunto encontrará el documento PDF con la información completa de la cotización.')
                ->attachContent($pdfContent, [
                    'fileName' => 'Cotizacion-' . $model->id . '.pdf',
                    'contentType' => 'application/pdf'
                ]);
            
            if ($message->send()) {
                return [
                    'success' => true,
                    'message' => 'Cotización enviada exitosamente a ' . Html::encode($email) . '.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al enviar el correo. Por favor, intente nuevamente.'
                ];
            }
        } catch (\Exception $e) {
            Yii::error('Error sending quotation email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate PDF content for quotation
     * @param Quotation $model
     * @return string PDF content
     */
    protected function generateQuotationPdf($model)
    {
        // Render PDF view
        $content = $this->renderPartial('_pdf', [
            'model' => $model,
        ]);

        // Setup footer HTML with contact info
        $footerHtml = '<table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; vertical-align: middle; font-size: 8pt; color: #666; padding: 5pt;">
                    Contáctenos: +506 4710-1005 | WhatsApp: +506 6060-7309 | San Ramón, Costa Rica.
                </td>
            </tr>
        </table>';

        // Setup mPDF
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
            'format' => \kartik\mpdf\Pdf::FORMAT_LETTER,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_STRING,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                body { font-family: Arial, sans-serif; font-size: 11pt; }
                h1 { color: #333; font-size: 20pt; margin-bottom: 10pt; }
                h2 { color: #555; font-size: 14pt; margin-top: 15pt; margin-bottom: 8pt; border-bottom: 1px solid #ddd; padding-bottom: 5pt; }
                .section { margin-bottom: 15pt; }
                img { max-width: 100%; height: auto; }
                table { width: 100%; border-collapse: collapse; margin: 10pt 0; }
                table td { padding: 5pt; border: 1px solid #ddd; }
            ',
            'marginFooter' => 30,
            'options' => ['title' => 'Cotización #' . $model->id],
            'methods' => [
                'SetHeader' => ['Cotización #' . $model->id],
                'SetFooter' => [$footerHtml],
            ]
        ]);

        return $pdf->output();
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

