<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Configuration;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConfigurationController implements the CRUD actions for Configuration model.
 */
class ConfigurationController extends Controller
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
                    'update' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Updates configuration.
     * @return mixed
     */
    public function actionIndex()
    {
        $whatsappNumber = Configuration::getValue('whatsapp_number', '1234567890');
        $siteTitle = Configuration::getValue('site_title', 'Tienda Online');
        $footerText = Configuration::getValue('footer_text', '© ' . date('Y') . ' Tienda Online. Todos los derechos reservados.');
        
        if (Yii::$app->request->post()) {
            $number = Yii::$app->request->post('whatsapp_number', '');
            $number = preg_replace('/[^0-9]/', '', $number); // Remove non-numeric characters
            $title = Yii::$app->request->post('site_title', 'Tienda Online');
            $footer = Yii::$app->request->post('footer_text', '');
            
            $success = true;
            
            if (!Configuration::setValue('whatsapp_number', $number)) {
                $success = false;
            }
            
            if (!Configuration::setValue('site_title', $title)) {
                $success = false;
            }
            
            if (!Configuration::setValue('footer_text', $footer)) {
                $success = false;
            }
            
            if ($success) {
                Yii::$app->session->setFlash('success', 'Configuración actualizada exitosamente.');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar la configuración.');
            }
        }

        return $this->render('index', [
            'whatsappNumber' => $whatsappNumber,
            'siteTitle' => $siteTitle,
            'footerText' => $footerText,
        ]);
    }
}

