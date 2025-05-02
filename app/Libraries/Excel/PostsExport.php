<?php

namespace App\Libraries\Excel;

use App\Libraries\ExportInterface;
use App\Models\PostModel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PostsExport implements ExportInterface
{
    protected $posts = [];

    public function __construct(array $posts = [])
    {
        if (empty($posts)) {
            // If no posts are provided, fetch all posts
            $postModel = new PostModel();
            $this->posts = $postModel->getPostWithUser();
        } else {
            $this->posts = $posts;
        }
    }

    /**
     * Export posts data to the worksheet
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function export(Worksheet $sheet): void
    {
        // Set column headers
        $headers = ['ID', 'Title', 'Content', 'Author', 'Created At', 'Updated At'];
        $sheet->fromArray($headers, null, 'A1');

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        
        $lastColumn = chr(64 + count($headers)); // Convert number to letter (e.g., 6 -> F)
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Set data rows
        $row = 2;
        foreach ($this->posts as $post) {
            $sheet->setCellValue('A' . $row, $post['id']);
            $sheet->setCellValue('B' . $row, $post['title']);
            
            // Clean content (remove HTML tags and limit length)
            $content = strip_tags($post['content']);
            if (strlen($content) > 1000) {
                $content = substr($content, 0, 1000) . '...';
            }
            $sheet->setCellValue('C' . $row, $content);
            
            $sheet->setCellValue('D' . $row, $post['username'] ?? 'Unknown');
            $sheet->setCellValue('E' . $row, $post['created_at']);
            $sheet->setCellValue('F' . $row, $post['updated_at']);
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set wrap text for content column
        $sheet->getStyle('C2:C' . ($row - 1))->getAlignment()->setWrapText(true);

        // Style the data rows with alternating colors
        for ($i = 2; $i < $row; $i++) {
            $style = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            
            if ($i % 2 == 0) {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
                ];
            }
            
            $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->applyFromArray($style);
        }
    }

    /**
     * Define spreadsheet properties
     *
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator' => 'CI4 Blog',
            'lastModifiedBy' => 'CI4 Blog',
            'title' => 'Blog Posts Export',
            'subject' => 'Blog Posts Export',
            'description' => 'List of blog posts from CI4 Blog',
            'keywords' => 'blog,posts,excel,export',
            'category' => 'Blog Exports',
        ];
    }
}