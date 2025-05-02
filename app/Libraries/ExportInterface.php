<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface ExportInterface
{
    /**
     * Export data to the given worksheet
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function export(Worksheet $sheet): void;
    
    /**
     * Optional: Define spreadsheet properties
     *
     * @return array
     */
    public function properties(): array;
}