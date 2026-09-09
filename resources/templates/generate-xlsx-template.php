<?php

require __DIR__.'/../../vendor/autoload.php';

use App\Services\CatalogImportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$ss = new Spreadsheet();
$sheet = $ss->getActiveSheet();
$sheet->setTitle('Produk');

foreach (CatalogImportService::HEADERS as $i => $header) {
    $sheet->setCellValue([$i + 1, 1], $header);
}

$target = __DIR__.'/../../storage/app/templates/katalog-produk-template.xlsx';
$writer = new Xlsx($ss);
$writer->save($target);

copy($target, __DIR__.'/katalog-produk-template.xlsx');

echo "Wrote {$target}\n";
