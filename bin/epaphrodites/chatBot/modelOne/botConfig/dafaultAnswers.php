<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait dafaultAnswers
{

    ///////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////// DFAULT MAIN BOT MESSAGES /////////////////////////////////////////////    

    /**
     * @param string $lang
     * @param object $class
     * @return array
    */
    public function epaphroditesDefaultMessageWhereNoResult(
        string $lang, 
        object $class
    ):array{

       return match ($lang) {

            'fr' => $class->defaultMessageInFrenchWhereNoAnswers(),

            default => $class->defaultMessageInEnglishWhereNoAnswers(),
        };
    }

    /**
     * @param string $lang
     * @param object $class
     * @return array
    */    
    public function epaphroditesDefaultMessageToGetMorePrecision(
        string $lang, 
        object $class
    ):array{

        return match ($lang) {

            'fr' => $class->defaultMessageInFrenchToGetMorePrecision(),

            default => $class->defaultMessageInEnglishToGetMorePrecision(),
        };
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////// DFAULT HEREDIA MESSAGES /////////////////////////////////////////////

    /**
     * @param string $lang
     * @param object $class
     * @return array
    */    
    public function herediaDefaultMessageWhereNoResult(
        string $lang, 
        object $class
    ):array{

        return match ($lang) {

            'fr' => $class->defaultMessageInFrenchWhereNoAnswers(),

            default => $class->defaultMessageInEnglishWhereNoAnswers(),
        };
    }

    /**
     * @param string $lang
     * @param object $class
     * @return array
    */    
    public function herediaDefaultMessageToGetMorePrecision(
        string $lang, 
        object $class
    ):array{

        return match ($lang) {

            'fr' => $class->defaultMessageInFrenchToGetMorePrecision(),

            default => $class->defaultMessageInEnglishToGetMorePrecision(),
        };
    }
}