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
        $dollarPrice = Configuration::getValue('dollar_price', '500.00');
        $showDollarPrice = Configuration::getValue('show_dollar_price', '0');
        
        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $success = true;

            // Update WhatsApp number
            $number = $post['whatsapp_number'] ?? '';
            $number = preg_replace('/[^0-9]/', '', $number);
            if (!Configuration::setValue('whatsapp_number', $number)) {
                $success = false;
            }

            // Update Site Title
            $newSiteTitle = $post['site_title'] ?? '';
            if (!Configuration::setValue('site_title', $newSiteTitle)) {
                $success = false;
            }

            // Update Footer Text
            $newFooterText = $post['footer_text'] ?? '';
            if (!Configuration::setValue('footer_text', $newFooterText)) {
                $success = false;
            }

            // Update Dollar Price
            $newDollarPrice = $post['dollar_price'] ?? '500.00';
            $newDollarPrice = preg_replace('/[^0-9.]/', '', $newDollarPrice);
            if (!Configuration::setValue('dollar_price', $newDollarPrice)) {
                $success = false;
            }

            // Update Show Dollar Price
            $newShowDollarPrice = isset($post['show_dollar_price']) ? '1' : '0';
            if (!Configuration::setValue('show_dollar_price', $newShowDollarPrice)) {
                $success = false;
            }
            
            if ($success) {
                Yii::$app->session->setFlash('success', 'Configuración actualizada exitosamente.');
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar la configuración.');
            }
            return $this->refresh();
        }

        return $this->render('index', [
            'whatsappNumber' => $whatsappNumber,
            'siteTitle' => $siteTitle,
            'footerText' => $footerText,
            'dollarPrice' => $dollarPrice,
            'showDollarPrice' => $showDollarPrice,
        ]);
    }
}

