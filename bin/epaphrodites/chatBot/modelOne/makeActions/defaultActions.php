<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\makeActions;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\othersActions;

trait defaultActions{

    use othersActions;
    /**
     * @param string $actionName, 
     * @param string $login
     */
    public function defaultActions(
        string $actionName, 
        string $login,
        string $lang
    ){
       return match ($actionName) {

            'clear' => $this->cleanJsonFile($login) === true ? "":"",
            'knowTime'=> $this->showTime(),
            'knowDate'=> $this->showDate($lang),
            default => null,
        };
    }
}