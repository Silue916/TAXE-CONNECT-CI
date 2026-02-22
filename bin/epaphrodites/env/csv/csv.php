<?php

namespace Epaphrodites\epaphrodites\env\csv;

use Epaphrodites\epaphrodites\env\csv\requests\addToCsv;
use Epaphrodites\epaphrodites\env\csv\requests\delCsv;
use Epaphrodites\epaphrodites\env\csv\requests\getCsv;
use Epaphrodites\epaphrodites\env\csv\requests\updCsv;
use Epaphrodites\epaphrodites\env\csv\saveLoad\loadCsv;
use Epaphrodites\epaphrodites\env\csv\saveLoad\saveCsvData;

class csv {

    use loadCsv, saveCsvData, addToCsv, delCsv, getCsv, updCsv;

    private $filePath;
    private $header = [];
    private $data = [];
    private $delimiter;

    public function __construct($filePath, $delimiter = ",") {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("Le fichier CSV est introuvable ou non lisible.");
        }
        $this->filePath = $filePath;
        $this->delimiter = $delimiter;

        $this->loadCSV();
    }

}
