<?php

namespace Epaphrodites\epaphrodites\env\csv\saveLoad;

trait saveCsvData {
       
    private function saveCSV() {

        if (($handle = fopen($this->filePath, 'w')) !== false) {
                
            fputcsv($handle, $this->header, $this->delimiter);
                
            foreach ($this->data as $row) {
                
                fputcsv($handle, $row, $this->delimiter);
            }

            fclose($handle);
        }
    }
}