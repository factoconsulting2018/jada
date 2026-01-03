<?php

/** @var yii\web\View $this */
/** @var array $stats */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashboard-index">
    <div class="admin-card">
        <h2>Estadísticas Generales</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['products'] ?></h3>
                <p>Total Productos</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['activeProducts'] ?></h3>
                <p>Productos Activos</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['categories'] ?></h3>
                <p>Categorías</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['clients'] ?></h3>
                <p>Clientes</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['banners'] ?></h3>
                <p>Banners Activos</p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Accesos Rápidos</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
            <a href="<?= \yii\helpers\Url::to(['/admin/product/create']) ?>" class="btn btn-primary">Nuevo Producto</a>
            <a href="<?= \yii\helpers\Url::to(['/admin/category/create']) ?>" class="btn btn-primary">Nueva Categoría</a>
            <a href="<?= \yii\helpers\Url::to(['/admin/banner/create']) ?>" class="btn btn-primary">Nuevo Banner</a>
            <a href="<?= \yii\helpers\Url::to(['/admin/client/create']) ?>" class="btn btn-primary">Nuevo Cliente</a>
        </div>
    </div>
</div>

