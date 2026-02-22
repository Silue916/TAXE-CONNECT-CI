<?php

namespace Epaphrodites\epaphrodites\env\csv\saveLoad;

trait loadCsv {

// Charger les données du CSV
    private function loadCSV() {
        if (($handle = fopen($this->filePath, 'r')) !== false) {
            $this->header = fgetcsv($handle, 1000, $this->delimiter); // Lecture de l'en-tête
            while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                $this->data[] = array_combine($this->header, $row);
            }
            fclose($handle);
        }
    }
}