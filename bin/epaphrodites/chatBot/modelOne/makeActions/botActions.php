<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\makeActions;

use Epaphrodites\epaphrodites\chatBot\modelOne\loadSave\dropJson;
use Epaphrodites\epaphrodites\chatBot\modelOne\loadSave\loadJson;

class botActions{

use defaultActions, dropJson, loadJson;

    /**
     * @param string $actionName, 
     * @param string $login, 
     * @param string $jsonFile
     */
    public function actions(
        string $actionName, 
        string $login, 
        string $jsonFile,
        string $lang
    ):void
    {

       match ($actionName) {

            'clear' => $this->cleanJsonFile($login, $jsonFile),
            'knowTime'=> $this->showTime(),
            'knowDate'=> $this->showDate($lang),
            default => null,
        };
    }
}