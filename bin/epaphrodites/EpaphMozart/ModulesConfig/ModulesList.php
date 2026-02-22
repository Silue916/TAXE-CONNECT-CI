<?php

namespace Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig;

use Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\Lists\GetModulesList;

class ModulesList extends GetModulesList
{

    private string $actions;

    /**
     * Liste des menus de l'application
     * @param int $key
     * @return array
     */
    public function ModulesLists(?string $key = null)
    {

        $list = static::GetModuleList();

        if ($key === null) {
            return $list;
        } else {
            return $list[$key];
        }
    }

    /**
     * @param mixed $keys
     * @param mixed $paths
     * @return string
     */
    public function SearchModules( ?string $keys = null , ?string $paths = null ) {

        $array = $this->YedidiahRightList();
        foreach ($array as $key => $value) {
            if (md5($_GET['_see'].','.$value[$keys]) == $paths) {
                $this->actions = $this->ModulesLists($value['apps']).' | '.$value['libelle'];
            }
        }

        return $this->actions; 
    } 
}