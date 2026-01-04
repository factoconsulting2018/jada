<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Configuration;
use app\models\SponsorBanner;
use app\models\ParallaxBackground;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
        
        // Get sponsor banners
        $sponsorBanners = [];
        for ($i = 1; $i <= 4; $i++) {
            $banner = SponsorBanner::getByPosition($i);
            if (!$banner) {
                $banner = new SponsorBanner();
                $banner->position = $i;
                $banner->status = SponsorBanner::STATUS_INACTIVE;
                $banner->save(false);
            }
            $sponsorBanners[$i] = $banner;
        }
        
        // Get parallax backgrounds for products page
        $productParallaxBackgrounds = [];
        for ($i = 1; $i <= 4; $i++) {
            $bg = ParallaxBackground::find()
                ->where(['section' => 'products_page', 'position' => $i])
                ->one();
            if (!$bg) {
                $bg = new ParallaxBackground();
                $bg->section = 'products_page';
                $bg->position = $i;
                $bg->status = ParallaxBackground::STATUS_INACTIVE;
                $bg->save(false);
            }
            $productParallaxBackgrounds[$i] = $bg;
        }
        
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
            
            // Update Sponsor Banners
            for ($i = 1; $i <= 4; $i++) {
                $banner = $sponsorBanners[$i];
                $banner->link = $post['sponsor_link_' . $i] ?? '';
                $banner->title = $post['sponsor_title_' . $i] ?? '';
                $banner->status = isset($post['sponsor_status_' . $i]) ? SponsorBanner::STATUS_ACTIVE : SponsorBanner::STATUS_INACTIVE;
                
                $banner->imageFile = UploadedFile::getInstanceByName("sponsor_image_{$i}");
                if ($banner->imageFile) {
                    if (!$banner->upload()) {
                        $success = false;
                    }
                }
                
                if (!$banner->save(false)) {
                    $success = false;
                }
            }
            
            // Update Product Parallax Backgrounds
            for ($i = 1; $i <= 4; $i++) {
                $bg = $productParallaxBackgrounds[$i];
                $bg->status = isset($post['product_parallax_status_' . $i]) ? ParallaxBackground::STATUS_ACTIVE : ParallaxBackground::STATUS_INACTIVE;
                
                $bg->imageFile = UploadedFile::getInstanceByName("product_parallax_image_{$i}");
                if ($bg->imageFile) {
                    if (!$bg->upload()) {
                        $success = false;
                    }
                }
                
                if (!$bg->save(false)) {
                    $success = false;
                }
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
            'sponsorBanners' => $sponsorBanners,
            'productParallaxBackgrounds' => $productParallaxBackgrounds,
        ]);
    }
}

