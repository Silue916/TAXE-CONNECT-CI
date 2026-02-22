<?php

namespace Epaphrodites\epaphrodites\define;

use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;

class SetTextMessages
{
    use currentFunctionNamespaces;

    /**
     * Get the answer for a given message code in the specified language.
     *
     * @param mixed $messageCode
     * @param string $lang
     * @return mixed
     */
    public function answers($messageCode, $lang = _LANG_)
    {
        $class = static::initNamespace();
        $lang = strtolower($lang);
        
        switch ($lang) {

            case 'eng':
                return $class['eng']->SwithAnswers($messageCode);

            case 'esp':
                return $class['spanish']->SwithAnswers($messageCode);
                
            default:
                return $class['french']->SwithAnswers($messageCode);
        }
    }
}
