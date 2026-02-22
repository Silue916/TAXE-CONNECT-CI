<?php

 namespace Epaphrodites\epaphrodites\Console\Stubs;

 class pythonStubs{

    /**
     * @param string $FilesNames
     * @param string $functionName
     * @return void
     */
    public static function Generate(string $FilesNames , string $functionName , string $fileInit):void
    {
       
       $stubs = static::stubs($functionName);
        static::AddToConfig($fileInit,$functionName);

        file_put_contents($FilesNames, $stubs);
    } 

    /**
     * @param string $functionName
     * @return string
     */
    public static function stubs(string $functionName):string
    {

        $stub = 
        "import sys
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class {$functionName}:

    def func_{$functionName}(self):
        print('Hello welcome to epaphrodites from Python!')

if __name__ == '__main__':  
    print_instance = {$functionName}()
    print_instance.func_{$functionName}()
        ";  
        
        return $stub;
    }

    public static function addToConfig(string $fileName, string $functionName): bool{

        $jsonConfigContent = file_get_contents(static::loadJsonConfig());

        $newJsonData = [
                "script" => "{$fileName}.py",
                "function" => "func_{$functionName}"
            ];

        $jsonConfigArray = json_decode($jsonConfigContent, true);

        $jsonConfigArray[$functionName] = $newJsonData;

        $jsonContent = json_encode($jsonConfigArray, JSON_PRETTY_PRINT);

        file_put_contents(static::loadJsonConfig(), $jsonContent);

        return true;
    }


    /**
     * Get JSON content from the config file.
     * @return string
     */
    private static function loadJsonConfig():string
    {
        $getFiles = _PYTHON_FILE_FOLDERS_ . 'config/config.json';

        return $getFiles;
    }    

 }