<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class ControllerStub{

    public static function GenerateControlleurs($FilesNames, $name , $controllerMaps)
    {
$stub = "<?php
namespace Epaphrodites\\controllers\\controllers;
        
use Epaphrodites\\controllers\\switchers\\MainSwitchers;
        
final class $name extends MainSwitchers
{
    private object \$msg;

    /**
    * Initialize object properties when an instance is created
    * @return void
    */    
    public final function __construct()
    {
        \$this->initializeObjects();
    }

    /**
    * Initialize each property using values retrieved from static configurations
    * @return void
    */
    private function initializeObjects(): void
    {
        \$this->msg = \$this->getFunctionObject(static::initNamespace(), 'msg');
    }       
        
    /**
     * Start exemple page
     * @param string \$html
     * @return void
    */      
    public final function exemplePages(string \$html): void
    {
        \$this->views( \$html, [], false );
    }     
        
}";
        
    file_put_contents($FilesNames, $stub);
    static::addToControllerMaps($controllerMaps, "\t\t\t'{$name}' => [ new {$name}, 'SwitchControllers', true, '{$name}Folder', _DIR_ADMIN_TEMP_ ],");
    static::addToControllerNamespace($controllerMaps, "use Epaphrodites\\controllers\\controllers\\{$name};");

    }

    /**
     * @param string $fileName
     * @param string $newFunctionContent
     * @param bool $isUp
     * @return void
     */  
    public static function addToControllerMaps(string $fileName, string $newFunctionContent)
    {
        $fileContent = file_get_contents($fileName);
    
        $lastMethodPosition = strrpos($fileContent, 'private function controllerMap');
    
        if ($lastMethodPosition !== false) {
            $openingBracketPosition = strpos($fileContent, '[', $lastMethodPosition);
    
            if ($openingBracketPosition !== false) {
                $fileContent = substr_replace($fileContent, "\n" . $newFunctionContent, $openingBracketPosition + 1, 0);
            }
        }
    
        file_put_contents($fileName, $fileContent, LOCK_EX);
    } 
    
    /**
     * @param string $fileName
     * @param string $newFunctionContent
     * @param bool $isUp
     * @return void
     */  
    public static function addToControllerNamespace(string $fileName, string $newFunctionContent)
    {
        $fileContent = file_get_contents($fileName);
        
        // Trouver la position du début du trait controllerMap
        $lastMethodPosition = strrpos($fileContent, 'trait controllerMap');
        
        if ($lastMethodPosition !== false) {
            // Trouver la position du début de la ligne précédant la déclaration du trait
            $startPosition = strrpos(substr($fileContent, 0, $lastMethodPosition), "\n\n");
    
            // Insérer le nouveau contenu juste après le début de la ligne
            $fileContent = substr_replace($fileContent, "\n" . $newFunctionContent, $startPosition, 0);
        }
        
        // Écrire le contenu modifié dans le fichier
        file_put_contents($fileName, $fileContent, LOCK_EX);
    }
}