<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * ReportController handles report generation
 */
class ReportController extends Controller
{
    /**
     * Lists available reports
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Generate products report in Excel format
     * @return \yii\web\Response
     */
    public function actionProducts()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');

        // Headers
        $headers = [
            'A' => 'ID',
            'B' => 'Nombre',
            'C' => 'Código',
            'D' => 'Categoría',
            'E' => 'Marca',
            'F' => 'Precio',
            'G' => 'Estado',
            'H' => 'Fecha Creación',
        ];

        // Set headers
        $col = 'A';
        $row = 1;
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->applyFromArray($headerStyle);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Get products
        $products = Product::find()
            ->with(['category', 'brand'])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $row = 2;
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->id);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('C' . $row, $product->code ?: '');
            $sheet->setCellValue('D' . $row, $product->category ? $product->category->name : '');
            $sheet->setCellValue('E' . $row, $product->brand ? $product->brand->name : '');
            $sheet->setCellValue('F' . $row, $product->price);
            $sheet->getCell('F' . $row)->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->setCellValue('G' . $row, $product->status == Product::STATUS_ACTIVE ? 'Activo' : 'Inactivo');
            $sheet->setCellValue('H' . $row, date('Y-m-d H:i:s', $product->created_at));
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($dataStyle);
            $row++;
        }

        // Add total row
        $totalRow = $row;
        $sheet->setCellValue('E' . $totalRow, 'TOTAL:');
        $sheet->getCell('E' . $totalRow)->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        
        // Add SUM formula for price
        $sheet->setCellValue('F' . $totalRow, '=SUM(F2:F' . ($row - 1) . ')');
        $sheet->getCell('F' . $totalRow)->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Count formulas
        $sheet->setCellValue('A' . ($totalRow + 1), 'TOTAL PRODUCTOS:');
        $sheet->getCell('A' . ($totalRow + 1))->getStyle()->applyFromArray([
            'font' => ['bold' => true],
        ]);
        $sheet->setCellValue('B' . ($totalRow + 1), '=COUNTA(B2:B' . ($row - 1) . ')');

        $sheet->setCellValue('A' . ($totalRow + 2), 'PRODUCTOS ACTIVOS:');
        $sheet->getCell('A' . ($totalRow + 2))->getStyle()->applyFromArray([
            'font' => ['bold' => true],
        ]);
        $sheet->setCellValue('B' . ($totalRow + 2), '=COUNTIF(G2:G' . ($row - 1) . ',"Activo")');

        // Generate file
        $filename = 'Reporte_Productos_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Generate clients report in Excel format
     * @return \yii\web\Response
     */
    public function actionClients()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Clientes');

        // Headers
        $headers = [
            'A' => 'ID',
            'B' => 'Tipo ID',
            'C' => 'Número ID',
            'D' => 'Nombre Completo',
            'E' => 'Email',
            'F' => 'WhatsApp',
            'G' => 'Teléfono',
            'H' => 'Dirección',
            'I' => 'Estado',
            'J' => 'Fecha Creación',
        ];

        // Set headers
        $row = 1;
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->applyFromArray($headerStyle);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Get clients
        $clients = Client::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $row = 2;
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $row, $client->id);
            $sheet->setCellValue('B' . $row, $client->getIdTypeLabel());
            $sheet->setCellValue('C' . $row, $client->id_number);
            $sheet->setCellValue('D' . $row, $client->full_name);
            $sheet->setCellValue('E' . $row, $client->email);
            $sheet->setCellValue('F' . $row, $client->whatsapp ?: '');
            $sheet->setCellValue('G' . $row, $client->phone ?: '');
            $sheet->setCellValue('H' . $row, $client->address ?: '');
            $sheet->setCellValue('I' . $row, $client->getStatusLabel());
            $sheet->setCellValue('J' . $row, date('Y-m-d H:i:s', $client->created_at));
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($dataStyle);
            $row++;
        }

        // Add total row
        $totalRow = $row;
        $sheet->setCellValue('H' . $totalRow, 'TOTAL CLIENTES:');
        $sheet->getCell('H' . $totalRow)->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->setCellValue('I' . $totalRow, '=COUNTA(D2:D' . ($row - 1) . ')');
        $sheet->getCell('I' . $totalRow)->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Add status breakdown
        $sheet->setCellValue('H' . ($totalRow + 1), 'CLIENTES PENDIENTES:');
        $sheet->getCell('H' . ($totalRow + 1))->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->setCellValue('I' . ($totalRow + 1), '=COUNTIF(I2:I' . ($row - 1) . ',"Pendiente")');

        $sheet->setCellValue('H' . ($totalRow + 2), 'CLIENTES ACEPTADOS:');
        $sheet->getCell('H' . ($totalRow + 2))->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->setCellValue('I' . ($totalRow + 2), '=COUNTIF(I2:I' . ($row - 1) . ',"Aceptado")');

        $sheet->setCellValue('H' . ($totalRow + 3), 'CLIENTES RECHAZADOS:');
        $sheet->getCell('H' . ($totalRow + 3))->getStyle()->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->setCellValue('I' . ($totalRow + 3), '=COUNTIF(I2:I' . ($row - 1) . ',"Rechazado")');

        // Generate file
        $filename = 'Reporte_Clientes_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}

