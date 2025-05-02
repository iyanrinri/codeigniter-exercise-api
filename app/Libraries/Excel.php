<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\HTTP\ResponseInterface;

class Excel
{
    /**
     * Download an Excel file with the given export instance
     *
     * @param ExportInterface $export The export instance to use
     * @param string $fileName The name of the file to download
     * @return ResponseInterface
     */
    public function download($export, string $fileName): ResponseInterface
    {
        $response = service('response');
        $spreadsheet = $this->createSpreadsheet($export);

        // Set headers for download
        $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->setHeader('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->setHeader('Cache-Control', 'max-age=0');

        // Create writer and output to PHP output stream
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $response->setBody(ob_get_clean());

        return $response;
    }

    /**
     * Create spreadsheet from export instance
     *
     * @param ExportInterface $export
     * @return Spreadsheet
     */
    private function createSpreadsheet($export): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set spreadsheet properties if method exists
        if (method_exists($export, 'properties')) {
            $properties = $export->properties();
            $spreadsheet->getProperties()
                ->setCreator($properties['creator'] ?? 'CI4 Excel')
                ->setLastModifiedBy($properties['lastModifiedBy'] ?? 'CI4 Excel')
                ->setTitle($properties['title'] ?? 'Excel Document')
                ->setSubject($properties['subject'] ?? 'Excel Document')
                ->setDescription($properties['description'] ?? 'Excel Document')
                ->setKeywords($properties['keywords'] ?? 'excel, export')
                ->setCategory($properties['category'] ?? 'export');
        }

        // Call export method to populate the sheet
        $export->export($sheet);
        
        return $spreadsheet;
    }
}