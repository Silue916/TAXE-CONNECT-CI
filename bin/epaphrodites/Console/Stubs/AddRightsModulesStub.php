<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class AddRightsModulesStub{

    public static function generate($FilesNames, $key , $libelle)
    {
        
        $FilesContent = file_get_contents($FilesNames);
        $lastBracketPosition = strrpos($FilesContent, ']');

        if ($lastBracketPosition !== false) {
            $FilesContent = substr($FilesContent, 0, $lastBracketPosition);
        }  
        
        file_put_contents($FilesNames,$FilesContent);
        $stub = static::AddToModuleList($key , $libelle);
        file_put_contents($FilesNames, $stub."\n }" , FILE_APPEND | LOCK_EX);
    }

    /**
     * @return string
    */
    private static function AddToModuleList($key , $libelle){

        $stub = 
        "    '$key' => '$libelle',  
        ];

     } ";
         
        return $stub;    
    }
}