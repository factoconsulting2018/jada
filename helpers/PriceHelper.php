<?php

namespace app\helpers;

use Yii;
use app\models\Configuration;

class PriceHelper
{
    /**
     * Get formatted price in colones with label
     * @param float $price
     * @return string
     */
    public static function formatColones($price)
    {
        return '₡' . number_format($price, 2, '.', ',');
    }

    /**
     * Get formatted price in dollars
     * @param float $priceInColones
     * @return string|null Returns null if dollar price is not configured
     */
    public static function formatDollars($priceInColones)
    {
        $dollarPrice = Configuration::getValue('dollar_price', '500.00');
        $showDollarPrice = Configuration::getValue('show_dollar_price', '0');

        if ($showDollarPrice != '1' || empty($dollarPrice) || !is_numeric($dollarPrice)) {
            return null;
        }

        $priceInDollars = $priceInColones / floatval($dollarPrice);
        return '$' . number_format($priceInDollars, 2, '.', ',');
    }

    /**
     * Get dollar price value
     * @return float
     */
    public static function getDollarPrice()
    {
        $dollarPrice = Configuration::getValue('dollar_price', '500.00');
        return floatval($dollarPrice);
    }

    /**
     * Check if dollar price should be shown
     * @return bool
     */
    public static function shouldShowDollarPrice()
    {
        $showDollarPrice = Configuration::getValue('show_dollar_price', '0');
        return $showDollarPrice == '1';
    }
}

