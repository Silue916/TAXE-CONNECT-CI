<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class AddUserRightsStub{

    public static function generate($FilesNames, $libelle , $path , $module)
    {
        
        $FilesContent = file_get_contents($FilesNames);
        $lastBracketPosition = strrpos($FilesContent, ']');

        if ($lastBracketPosition !== false) {
            $FilesContent = substr($FilesContent, 0, $lastBracketPosition);
        }  
        
        file_put_contents($FilesNames,$FilesContent);
        $stub = static::AddToRightList($libelle , $path , $module);
        file_put_contents($FilesNames, $stub."\n }" , FILE_APPEND | LOCK_EX);
    }

    /**
     * @return string
    */
    private static function AddToRightList($libelle , $path , $module){

        $stub = 
        "    [
                'apps' => '$module', 
                'libelle' => '$libelle', 
                'path' => '$path', 
             ],
        ];
        
    }";
         
        return $stub;    
    }
}