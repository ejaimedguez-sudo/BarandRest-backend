<?php
require __DIR__ . '/../vendor/autoload.php';
// Bootstrap minimal app container for helpers
if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
    $app = require __DIR__ . '/../bootstrap/app.php';
}

use App\Exports\ReportExport;

// Sample rows
$rows = [
    ['date' => '2026-02-05', 'item' => 'Test Cocktail', 'quantity' => 1, 'unit_price' => 12.0],
    ['date' => '2026-02-05', 'item' => 'Snack', 'quantity' => 2, 'unit_price' => 4.5],
];

$export = new ReportExport($rows);
$xlsxPath = __DIR__ . '/../storage/app/reports/test_report.xlsx';
@mkdir(dirname($xlsxPath), 0777, true);
try {
    $export->save($xlsxPath);
    echo "XLSX generado: $xlsxPath\n";
} catch (\Throwable $e) {
    echo "Error generando XLSX: " . $e->getMessage() . "\n";
}

echo "Prueba completada.\n";
