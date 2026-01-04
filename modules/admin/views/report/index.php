<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Reportes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">
    <div class="admin-card">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <p style="color: #666; margin-bottom: 2rem;">Seleccione el reporte que desea generar en formato Excel:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <!-- Reporte de Productos -->
            <div style="background: white; border: 2px solid #e0e0e0; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span class="material-icons" style="font-size: 40px; color: white;">inventory_2</span>
                    </div>
                    <h2 style="margin: 0; color: #333; font-size: 1.5rem;">Reporte de Productos</h2>
                </div>
                <p style="color: #666; text-align: center; margin-bottom: 1.5rem;">
                    Genera un archivo Excel con toda la información de los productos, incluyendo:
                </p>
                <ul style="color: #666; margin-bottom: 1.5rem; padding-left: 1.5rem;">
                    <li>ID, Nombre, Código</li>
                    <li>Categoría y Marca</li>
                    <li>Precio</li>
                    <li>Estado</li>
                    <li>Fórmulas de totalización</li>
                </ul>
                <div style="text-align: center;">
                    <?= Html::a('<span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">download</span> Generar Reporte', ['products'], [
                        'class' => 'btn btn-primary',
                        'style' => 'padding: 0.75rem 2rem; font-size: 1rem;'
                    ]) ?>
                </div>
            </div>
            
            <!-- Reporte de Clientes -->
            <div style="background: white; border: 2px solid #e0e0e0; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span class="material-icons" style="font-size: 40px; color: white;">people</span>
                    </div>
                    <h2 style="margin: 0; color: #333; font-size: 1.5rem;">Reporte de Clientes</h2>
                </div>
                <p style="color: #666; text-align: center; margin-bottom: 1.5rem;">
                    Genera un archivo Excel con toda la información de los clientes, incluyendo:
                </p>
                <ul style="color: #666; margin-bottom: 1.5rem; padding-left: 1.5rem;">
                    <li>Datos de identificación</li>
                    <li>Información de contacto</li>
                    <li>Estado del cliente</li>
                    <li>Fechas de registro</li>
                    <li>Fórmulas de totalización</li>
                </ul>
                <div style="text-align: center;">
                    <?= Html::a('<span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">download</span> Generar Reporte', ['clients'], [
                        'class' => 'btn btn-primary',
                        'style' => 'padding: 0.75rem 2rem; font-size: 1rem;'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.report-index .admin-card > div > div:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
    .report-index .admin-card > div {
        grid-template-columns: 1fr;
    }
}
</style>

