<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait othersActions
{

    /**
     * @return string
     */
    public function showTime():string{

        return date("H:i:s");
    }

    /**
     * @param string $lang
     * @return string
    */
    public function showDate(
        string $lang
    ):string{

        $setLocal = $lang==="fr" ? "fr_FR.utf8" : "en_US.utf8";

        return (new \IntlDateFormatter($setLocal, \IntlDateFormatter::FULL, \IntlDateFormatter::NONE))->format(time());
    }
}