<?php

namespace Epaphrodites\epaphrodites\env\csv\requests;

trait addToCsv{

    
    public function insert($rowData) {

        if (array_diff_key(array_flip($this->header), $rowData)) {
            
            throw new \Exception("Les données fournies ne correspondent pas à l'en-tête du CSV.");
        }

        $this->data[] = $rowData;

        $this->saveCSV();

        return true;
    }
}