<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\translate;

use Epaphrodites\epaphrodites\env\config\GeneralConfig;
use InvalidArgumentException;
use RuntimeException;

class PythonCodesTranslate extends GeneralConfig
{
    
    /**
     * Execute python scripts
     * 
     * @param string|null $pyFunction
     * @param array $data
     * @param bool $useStreaming
     * @return mixed
     */
    public function executePython(
        string|null $pyFunction = null, 
        array $data = [],
        bool $useStreaming = false
    ): mixed {
        $getJsonContent = $this->loadJsonConfig();
     
        if (!empty($getJsonContent[$pyFunction])) {
            $scriptInfo = $getJsonContent[$pyFunction];
            $mergedDatas = array_merge(['function' => $scriptInfo["function"]], $data);
            
            $callback = $useStreaming ? function ($chunk) {
                echo $chunk;
                flush();
                ob_flush();
            } : null;
            
            return $this->pythonSystemCode(
                _PYTHON_FILE_FOLDERS_ . $scriptInfo["script"], 
                $mergedDatas,
                $callback,
                $useStreaming
            );
        } else {
            return false;
        }
    }

    /**
     * Get JSON content from the config file.
     * @return array
     */
    private function loadJsonConfig(): array
    {
        $getFiles = _PYTHON_FILE_FOLDERS_ . 'config/config.json';

        return json_decode(file_get_contents($getFiles), true);
    }
}