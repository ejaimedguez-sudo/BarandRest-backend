<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExport
{
    protected $rows;

    public function __construct(array $rows = [])
    {
        $this->rows = $rows;
    }

    public function save(string $path)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        // Header: title and optional logo
        $title = 'Reporte - '.now()->format('Y-m-d H:i');
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension(1)->setRowHeight(30);
        // embed logo if exists
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing;
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(40);
            $drawing->setCoordinates('E1');
            $drawing->setWorksheet($sheet);
        }
        $startRow = 3;
        // If rows is associative array (e.g., ['data' => [...]]) try to extract
        $rows = $this->rows;
        if (isset($rows['data']) && is_array($rows['data'])) {
            $rows = $rows['data'];
        }

        // If first row contains headers
        $rowNum = $startRow;
        if (! empty($rows)) {
            // If rows is an array of arrays or objects
            $first = $rows[0];
            if (is_array($first)) {
                $headers = array_keys($first);
                $col = 1;
                foreach ($headers as $h) {
                    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col).$rowNum;
                    $sheet->setCellValue($cell, $h);
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                    $col++;
                }
                $rowNum++;
                foreach ($rows as $r) {
                    $col = 1;
                    foreach ($headers as $h) {
                        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col).$rowNum;
                        $sheet->setCellValue($cell, $r[$h] ?? '');
                        $col++;
                    }
                    $rowNum++;
                }
                // Totals row for numeric columns
                $numericCols = [];
                foreach ($headers as $idx => $h) {
                    // detect numeric by scanning first few rows
                    for ($i = $startRow + 1; $i <= min($startRow + 20, $rowNum); $i++) {
                        $val = $sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1).$i)->getValue();
                        if (is_numeric($val)) {
                            $numericCols[] = $idx + 1;
                            break;
                        }
                    }
                }
                if (! empty($numericCols)) {
                    $sheet->setCellValue('A'.$rowNum, 'Totales');
                    foreach ($numericCols as $colIndex) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                        $fromCell = $colLetter.($startRow + 1);
                        $toCell = $colLetter.($rowNum - 1);
                        $sheet->setCellValue($colLetter.$rowNum, "=SUM($fromCell:$toCell)");
                        // format currency for common headers
                        $header = strtolower($headers[$colIndex - 1]);
                        if (str_contains($header, 'price') || str_contains($header, 'amount') || str_contains($header, 'total') || str_contains($header, 'cost')) {
                            $sheet->getStyle($colLetter.($startRow + 1).':'.$colLetter.$rowNum)
                                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                        }
                    }
                }
                // Auto-size columns
                $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
                for ($c = 1; $c <= count($headers); $c++) {
                    $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
                }
                // Freeze header row and enable auto-filter
                $sheet->freezePane('A'.($startRow + 1));
                $sheet->setAutoFilter('A'.($startRow).':'.$highestColumn.($rowNum - 1));
                // Set document properties
                $props = $spreadsheet->getProperties();
                $props->setCreator(config('app.name'));
                $props->setTitle($title);
            } else {
                // fallback: write each row as a single cell per row
                foreach ($rows as $r) {
                    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1).$rowNum;
                    $sheet->setCellValue($cell, is_scalar($r) ? $r : json_encode($r));
                    $rowNum++;
                }
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);
    }
}
