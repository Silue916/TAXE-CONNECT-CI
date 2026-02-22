<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class AddNoSqlRequestStub extends SqlStub{

    public static function generate($FilesNames, $name, $type)
    {
        $FilesContent = file_get_contents($FilesNames);
    
        $lastBracketPosition = strrpos($FilesContent, '}');
        if ($lastBracketPosition !== false) {
            $FilesContent = substr($FilesContent, 0, $lastBracketPosition);
        }
    
        file_put_contents($FilesNames, $FilesContent);
    
        $stub = static::SwicthNoSqlRequestContent($type, $name);
        file_put_contents($FilesNames, $stub . "\n}\n", FILE_APPEND | LOCK_EX);
    }
}