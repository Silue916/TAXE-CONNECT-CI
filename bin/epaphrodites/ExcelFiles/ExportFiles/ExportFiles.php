<?php

namespace Epaphrodites\epaphrodites\ExcelFiles\ExportFiles;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportFiles
{
    private Spreadsheet $spreadsheet;
    private \PhpOffice\PhpSpreadsheet\Writer\Xls $Xls;
    private \PhpOffice\PhpSpreadsheet\Writer\Xlsx $Xlsx;
    /**
     * Get class
     * @return void
    */
    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet; 
        $this->Xlsx = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
        $this->Xls = new \PhpOffice\PhpSpreadsheet\Writer\Xls($this->spreadsheet);
    }

    /**
     * Export file
     *
     * @var mixed $sheet
     * @param array $datas
     * @param array $title
     * @var \PhpOffice\PhpSpreadsheet\Writer\Xlsx $writer
     * @var mixed $sheet
     * @return void
     */
    public function exportExcelFiles( array $header , array $content , string $title , ?string $type = 'xlsx' )
    {

        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->fromArray( $header , NULL , 'A1' );
            
        $sheet->fromArray( $content , NULL, 'A2');

        $filename = $title.'_'.date('d/m/Y').'.'.$type;

        header('Content-Type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="'.$filename.'"'); 

        header('Cache-Control: max-age=0');

        $type ==='xls' ? $this->Xls->save('php://output') : $this->Xlsx->save('php://output');
        
        exit();    
    }
}