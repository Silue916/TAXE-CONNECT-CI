<?php

namespace Epaphrodites\epaphrodites\ExcelFiles\ImportFiles;

use Epaphrodites\epaphrodites\env\config\GeneralConfig;

class ImportFiles extends FilesExtension
{

    /**
     * Summary of importExcelFiles
     * 
     * @param string $ExcelFiles
     * @param string $key
     * @return array
     */
    public function importExcelFiles( 
        string $ExcelFiles, 
        string $key = '__file__' 
    ):array{

        if(isset($ExcelFiles) && in_array($_FILES[$key]['type'], static::$FilesMimes)) 
        {

            $GetReader = $this->ExtenstionFiles($ExcelFiles);

            if($GetReader!==false){

                $SpreadSheet = $GetReader->load($_FILES[$key]['tmp_name']);

                return $SpreadSheet->getActiveSheet()->toArray();

            }else{return []; }
            
        }else{return []; }
    }

    /**
     * Summary of importLargeExcelFiles
     * To use this function, you must install python 3
     * and run this command "pip install odfpy"
     * 
     * @param string $ExcelFiles
     * @param string $key
     * @return array|bool
     */     
    public function importLargeExcelFiles(
        string $ExcelFiles, 
        string $key = '__file__' 
    ):array{

        if(isset($ExcelFiles) && in_array($_FILES[$key]['type'], static::$FilesMimes)) 
        {        

            $filePath = $_FILES[$key]['tmp_name'];
            
            if (!file_exists($filePath)) {

                throw new \Exception("Excel paths are not valid.");
            }
            
            $Extension = (new GeneralConfig)->EndFiles($ExcelFiles , '.');

            $result = static::initConfig()['python']->executePython('importExcelFiles', ["excel" => $filePath, 'ext' => ".{$Extension}"]);

            return is_array($result) ? $result : [];
    
        }else{return []; }
    }
}