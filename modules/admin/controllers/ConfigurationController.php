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
        $blockTitle = Configuration::getValue('block_title', '');
        $blockContent = Configuration::getValue('block_content', '');
        
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
        
        // Get parallax backgrounds for all sections
        $parallaxSections = ParallaxBackground::getSections();
        $parallaxBackgroundsBySection = [];
        foreach ($parallaxSections as $sectionKey => $sectionLabel) {
            $parallaxBackgroundsBySection[$sectionKey] = [];
            for ($i = 1; $i <= 4; $i++) {
                $bg = ParallaxBackground::find()
                    ->where(['section' => $sectionKey, 'position' => $i])
                    ->one();
                if (!$bg) {
                    $bg = new ParallaxBackground();
                    $bg->section = $sectionKey;
                    $bg->position = $i;
                    $bg->status = ParallaxBackground::STATUS_INACTIVE;
                    $bg->save(false);
                }
                $parallaxBackgroundsBySection[$sectionKey][$i] = $bg;
            }
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

            // Update Section Visibility
            $sectionBanner = isset($post['section_banner']) ? '1' : '0';
            if (!Configuration::setValue('section_banner', $sectionBanner)) {
                $success = false;
            }

            $sectionProducts = isset($post['section_products']) ? '1' : '0';
            if (!Configuration::setValue('section_products', $sectionProducts)) {
                $success = false;
            }

            $sectionCategories = isset($post['section_categories']) ? '1' : '0';
            if (!Configuration::setValue('section_categories', $sectionCategories)) {
                $success = false;
            }

            $sectionSponsors = isset($post['section_sponsors']) ? '1' : '0';
            if (!Configuration::setValue('section_sponsors', $sectionSponsors)) {
                $success = false;
            }

            // Update Block Title
            $newBlockTitle = $post['block_title'] ?? '';
            if (!Configuration::setValue('block_title', $newBlockTitle)) {
                $success = false;
            }

            // Update Block Content
            $newBlockContent = $post['block_content'] ?? '';
            if (!Configuration::setValue('block_content', $newBlockContent)) {
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
            
            // Update Parallax Backgrounds for all sections
            foreach ($parallaxSections as $sectionKey => $sectionLabel) {
                for ($i = 1; $i <= 4; $i++) {
                    $bg = $parallaxBackgroundsBySection[$sectionKey][$i];
                    $bg->status = isset($post['parallax_status_' . $sectionKey . '_' . $i]) ? ParallaxBackground::STATUS_ACTIVE : ParallaxBackground::STATUS_INACTIVE;
                    
                    // Update overlay color
                    $overlayColor = $post['parallax_overlay_color_' . $sectionKey . '_' . $i] ?? '';
                    if (!empty($overlayColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $overlayColor)) {
                        $bg->overlay_color = $overlayColor;
                    } else {
                        $bg->overlay_color = null;
                    }
                    
                    // Update overlay opacity
                    $overlayOpacity = $post['parallax_overlay_opacity_' . $sectionKey . '_' . $i] ?? '0.3';
                    $overlayOpacity = floatval($overlayOpacity);
                    if ($overlayOpacity >= 0 && $overlayOpacity <= 1) {
                        $bg->overlay_opacity = $overlayOpacity;
                    } else {
                        $bg->overlay_opacity = 0.3;
                    }
                    
                    $bg->imageFile = UploadedFile::getInstanceByName("parallax_image_{$sectionKey}_{$i}");
                    if ($bg->imageFile) {
                        if (!$bg->upload()) {
                            $success = false;
                        }
                    }
                    
                    if (!$bg->save(false)) {
                        $success = false;
                    }
                }
            }
            
            if ($success) {
                Yii::$app->session->setFlash('success', 'Configuración actualizada exitosamente.');
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar la configuración.');
            }
            return $this->refresh();
        }

        // Get section visibility settings
        $showSectionBanner = Configuration::getValue('section_banner', '1');
        $showSectionProducts = Configuration::getValue('section_products', '1');
        $showSectionCategories = Configuration::getValue('section_categories', '1');
        $showSectionSponsors = Configuration::getValue('section_sponsors', '1');

        return $this->render('index', [
            'whatsappNumber' => $whatsappNumber,
            'siteTitle' => $siteTitle,
            'footerText' => $footerText,
            'dollarPrice' => $dollarPrice,
            'showDollarPrice' => $showDollarPrice,
            'sponsorBanners' => $sponsorBanners,
            'parallaxSections' => $parallaxSections,
            'parallaxBackgroundsBySection' => $parallaxBackgroundsBySection,
            'showSectionBanner' => $showSectionBanner,
            'showSectionProducts' => $showSectionProducts,
            'showSectionCategories' => $showSectionCategories,
            'showSectionSponsors' => $showSectionSponsors,
            'blockTitle' => $blockTitle,
            'blockContent' => $blockContent,
        ]);
    }
}

