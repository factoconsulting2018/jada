<?php

/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $visitsByCountry */
/** @var array $dailyVisits */
/** @var array $topPages */
/** @var array $visitsByHour */

use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for JavaScript
$visitsByCountryJson = Json::encode($visitsByCountry);
$dailyVisitsJson = Json::encode($dailyVisits);
$visitsByHourJson = Json::encode($visitsByHour);
?>

<div class="dashboard-container" data-theme="light">
    <!-- Theme Toggle -->
    <div class="theme-toggle-container">
        <button id="theme-toggle" class="theme-toggle-btn" title="Cambiar tema">
            <span class="material-icons">brightness_4</span>
        </button>
    </div>

    <!-- KPI Cards -->
    <div class="dashboard-kpis">
        <!-- Productos -->
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span class="material-icons">inventory_2</span>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= number_format($stats['products']) ?></div>
                <div class="kpi-label">Total Productos</div>
                <div class="kpi-sublabel"><?= number_format($stats['activeProducts']) ?> activos</div>
            </div>
        </div>
        
        <!-- Clientes -->
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <span class="material-icons">people</span>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= number_format($stats['clients']) ?></div>
                <div class="kpi-label">Clientes</div>
                <div class="kpi-sublabel">
                    <span style="color: #ffc107;"><?= number_format($stats['pendingClients'] ?? 0) ?> pendientes</span>
                    <span style="margin: 0 0.5rem;">•</span>
                    <span style="color: #4caf50;"><?= number_format($stats['acceptedClients'] ?? 0) ?> aceptados</span>
                </div>
            </div>
        </div>
        
        <!-- Cotizaciones -->
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <span class="material-icons">request_quote</span>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= number_format($stats['quotations']) ?></div>
                <div class="kpi-label">Cotizaciones</div>
                <div class="kpi-sublabel"><?= number_format($stats['pendingQuotations']) ?> pendientes</div>
            </div>
        </div>
        
        <!-- Visitas -->
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <span class="material-icons">visibility</span>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= number_format($stats['totalVisits'] ?? 0) ?></div>
                <div class="kpi-label">Visitas Totales</div>
                <div class="kpi-sublabel"><?= number_format($stats['last30DaysVisits'] ?? 0) ?> últimos 30 días</div>
            </div>
        </div>
        
        <!-- Visitas 7 días -->
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <span class="material-icons">trending_up</span>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= number_format($stats['last7DaysVisits'] ?? 0) ?></div>
                <div class="kpi-label">Visitas (7 días)</div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="dashboard-row">
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h3>Visitas Diarias (Últimos 30 días)</h3>
            </div>
            <div class="card-body">
                <canvas id="dailyVisitsChart"></canvas>
            </div>
        </div>
        
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h3>Visitas por Hora (Últimos 7 días)</h3>
            </div>
            <div class="card-body">
                <canvas id="hourlyVisitsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Map and Top Pages Row -->
    <div class="dashboard-row">
        <div class="dashboard-card map-card">
            <div class="card-header">
                <h3>Mapa de Visitas por País</h3>
            </div>
            <div class="card-body">
                <div id="visitsMap" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Páginas Más Visitadas</h3>
            </div>
            <div class="card-body">
                <div class="top-pages-list">
                    <?php foreach ($topPages as $index => $page): ?>
                        <div class="top-page-item">
                            <span class="page-rank"><?= $index + 1 ?></span>
                            <div class="page-info">
                                <div class="page-name"><?= Html::encode($page['page'] ?: 'Página principal') ?></div>
                                <div class="page-count"><?= number_format($page['count']) ?> visitas</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($topPages)): ?>
                        <div class="no-data">No hay datos de visitas disponibles</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Leaflet for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php
$this->registerCss("
.dashboard-container {
    padding: 2rem;
    transition: background-color 0.3s, color 0.3s;
}

.dashboard-container[data-theme='dark'] {
    background-color: #1e1e1e;
    color: #ffffff;
}

.theme-toggle-container {
    position: fixed;
    top: 80px;
    right: 2rem;
    z-index: 1000;
}

.theme-toggle-btn {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.dashboard-container[data-theme='dark'] .theme-toggle-btn {
    background: #2d2d2d;
    border-color: #404040;
    color: #ffffff;
}

.theme-toggle-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.dashboard-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.dashboard-container[data-theme='dark'] .kpi-card {
    background: #2d2d2d;
    color: #ffffff;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.kpi-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.kpi-icon .material-icons {
    font-size: 32px;
}

.kpi-content {
    flex: 1;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.kpi-label {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.dashboard-container[data-theme='dark'] .kpi-label {
    color: #aaaaaa;
}

.kpi-sublabel {
    font-size: 0.75rem;
    color: #999;
    margin-top: 0.25rem;
}

.dashboard-container[data-theme='dark'] .kpi-sublabel {
    color: #777777;
}

.dashboard-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.dashboard-container[data-theme='dark'] .dashboard-card {
    background: #2d2d2d;
    color: #ffffff;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
}

.dashboard-container[data-theme='dark'] .card-header {
    border-bottom-color: #404040;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 500;
}

.card-body {
    padding: 1.5rem;
}

.chart-card {
    min-height: 400px;
}

.map-card {
    min-height: 400px;
}

.top-pages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.top-page-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    background: #f5f5f5;
    transition: background 0.3s;
}

.dashboard-container[data-theme='dark'] .top-page-item {
    background: #1e1e1e;
}

.top-page-item:hover {
    background: #e0e0e0;
}

.dashboard-container[data-theme='dark'] .top-page-item:hover {
    background: #333333;
}

.page-rank {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--md-sys-color-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.page-info {
    flex: 1;
}

.page-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.page-count {
    font-size: 0.875rem;
    color: #666;
}

.dashboard-container[data-theme='dark'] .page-count {
    color: #aaaaaa;
}

.stats-grid-mini {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.stat-mini {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f5f5f5;
}

.dashboard-container[data-theme='dark'] .stat-mini {
    background: #1e1e1e;
}

.stat-mini-label {
    display: block;
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.dashboard-container[data-theme='dark'] .stat-mini-label {
    color: #aaaaaa;
}

.stat-mini-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 600;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: #999;
}

.dashboard-container[data-theme='dark'] .no-data {
    color: #666;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .dashboard-row {
        grid-template-columns: 1fr;
    }
    
    .dashboard-kpis {
        grid-template-columns: 1fr;
    }
    
    .theme-toggle-container {
        top: 70px;
        right: 1rem;
    }
}
");

$this->registerJs("
// Theme Toggle
const themeToggle = document.getElementById('theme-toggle');
const dashboardContainer = document.querySelector('.dashboard-container');
const themeIcon = themeToggle.querySelector('.material-icons');

// Load saved theme
const savedTheme = localStorage.getItem('dashboardTheme') || 'light';
dashboardContainer.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);

themeToggle.addEventListener('click', function() {
    const currentTheme = dashboardContainer.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    dashboardContainer.setAttribute('data-theme', newTheme);
    localStorage.setItem('dashboardTheme', newTheme);
    updateThemeIcon(newTheme);
    updateChartsTheme(newTheme);
    updateMapTheme(newTheme);
});

function updateThemeIcon(theme) {
    themeIcon.textContent = theme === 'light' ? 'brightness_4' : 'brightness_6';
}

// Chart.js Configuration
const chartColors = {
    light: {
        grid: '#e0e0e0',
        text: '#333333',
        background: '#ffffff'
    },
    dark: {
        grid: '#404040',
        text: '#ffffff',
        background: '#2d2d2d'
    }
};

function getChartConfig(theme) {
    const colors = chartColors[theme];
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: colors.text
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: colors.text
                },
                grid: {
                    color: colors.grid
                }
            },
            y: {
                ticks: {
                    color: colors.text
                },
                grid: {
                    color: colors.grid
                }
            }
        }
    };
}

// Daily Visits Chart
const dailyVisitsData = " . $dailyVisitsJson . ";
const dailyVisitsCtx = document.getElementById('dailyVisitsChart');
const dailyVisitsChart = new Chart(dailyVisitsCtx, {
    type: 'line',
    data: {
        labels: dailyVisitsData.map(d => d.date),
        datasets: [{
            label: 'Visitas',
            data: dailyVisitsData.map(d => d.count),
            borderColor: 'rgb(103, 80, 164)',
            backgroundColor: 'rgba(103, 80, 164, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: getChartConfig(savedTheme)
});

// Hourly Visits Chart
const hourlyVisitsData = " . $visitsByHourJson . ";
const hourlyVisitsCtx = document.getElementById('hourlyVisitsChart');
const hourlyVisitsChart = new Chart(hourlyVisitsCtx, {
    type: 'bar',
    data: {
        labels: hourlyVisitsData.map(d => d.hour + ':00'),
        datasets: [{
            label: 'Visitas',
            data: hourlyVisitsData.map(d => d.count),
            backgroundColor: 'rgba(103, 80, 164, 0.6)',
            borderColor: 'rgb(103, 80, 164)',
            borderWidth: 1
        }]
    },
    options: getChartConfig(savedTheme)
});

function updateChartsTheme(theme) {
    const config = getChartConfig(theme);
    dailyVisitsChart.options = config;
    hourlyVisitsChart.options = config;
    dailyVisitsChart.update();
    hourlyVisitsChart.update();
}

// Map
const visitsByCountry = " . $visitsByCountryJson . ";
const map = L.map('visitsMap').setView([10, -84], 3);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

visitsByCountry.forEach(function(country) {
    if (country.avg_lat && country.avg_lng) {
        const marker = L.circleMarker([country.avg_lat, country.avg_lng], {
            radius: Math.min(country.count / 10, 30),
            fillColor: '#6750A4',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.7
        }).addTo(map);
        
        marker.bindPopup('<strong>' + country.country + '</strong><br>' + country.count + ' visitas');
    }
});

function updateMapTheme(theme) {
    // Map theme update if needed
}
");
?>
