<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\Category;
use app\models\Client;
use app\models\Banner;
use app\models\Quotation;
use app\models\Visit;
use yii\db\Expression;

/**
 * Dashboard controller for the `admin` module
 */
class DashboardController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $stats = [
            'products' => Product::find()->count(),
            'activeProducts' => Product::find()->where(['status' => Product::STATUS_ACTIVE])->count(),
            'categories' => Category::find()->count(),
            'clients' => Client::find()->count(),
            'pendingClients' => Client::find()->where(['status' => Client::STATUS_PENDING])->count(),
            'acceptedClients' => Client::find()->where(['status' => Client::STATUS_ACCEPTED])->count(),
            'quotations' => Quotation::find()->count(),
            'pendingQuotations' => Quotation::find()->where(['status' => Quotation::STATUS_PENDING])->count(),
            'banners' => Banner::find()->where(['status' => Banner::STATUS_ACTIVE])->count(),
        ];

        // Get visit statistics
        $totalVisits = Visit::find()->count();
        $last30DaysVisits = Visit::find()
            ->where(['>=', 'created_at', time() - (30 * 24 * 60 * 60)])
            ->count();
        
        $last7DaysVisits = Visit::find()
            ->where(['>=', 'created_at', time() - (7 * 24 * 60 * 60)])
            ->count();

        // Get visits by country (for map)
        $visitsByCountry = Visit::find()
            ->select(['country', 'COUNT(*) as count', 'AVG(latitude) as avg_lat', 'AVG(longitude) as avg_lng'])
            ->where(['IS NOT', 'country', null])
            ->groupBy('country')
            ->orderBy(['count' => SORT_DESC])
            ->limit(50)
            ->asArray()
            ->all();

        // Get daily visits for the last 30 days (for chart)
        $dailyVisits = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', time() - ($i * 24 * 60 * 60));
            $startOfDay = strtotime($date . ' 00:00:00');
            $endOfDay = strtotime($date . ' 23:59:59');
            
            $count = Visit::find()
                ->where(['>=', 'created_at', $startOfDay])
                ->andWhere(['<=', 'created_at', $endOfDay])
                ->count();
            
            $dailyVisits[] = [
                'date' => $date,
                'count' => $count,
            ];
        }

        // Get top pages
        $topPages = Visit::find()
            ->select(['page', 'COUNT(*) as count'])
            ->where(['IS NOT', 'page', null])
            ->groupBy('page')
            ->orderBy(['count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Get visits by hour (for chart) - simplified version
        $visitsByHour = [];
        $sevenDaysAgo = time() - (7 * 24 * 60 * 60);
        $recentVisits = Visit::find()
            ->where(['>=', 'created_at', $sevenDaysAgo])
            ->all();
        
        $hourlyCounts = array_fill(0, 24, 0);
        foreach ($recentVisits as $visit) {
            $hour = (int)date('G', $visit->created_at);
            $hourlyCounts[$hour]++;
        }
        
        for ($hour = 0; $hour < 24; $hour++) {
            $visitsByHour[] = [
                'hour' => $hour,
                'count' => $hourlyCounts[$hour],
            ];
        }

        $stats['totalVisits'] = $totalVisits;
        $stats['last30DaysVisits'] = $last30DaysVisits;
        $stats['last7DaysVisits'] = $last7DaysVisits;

        return $this->render('index', [
            'stats' => $stats,
            'visitsByCountry' => $visitsByCountry,
            'dailyVisits' => $dailyVisits,
            'topPages' => $topPages,
            'visitsByHour' => $visitsByHour,
        ]);
    }
}
