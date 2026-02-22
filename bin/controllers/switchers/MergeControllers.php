<?php

declare(strict_types=1);

namespace Epaphrodites\controllers\switchers;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\heredia\SwitchersHeredia;

class MergeControllers extends epaphroditeClass implements contractController
{

    /**
     * @param object $class
     * @param mixed $pages
     * @param bool $switch
     * @return mixed
     */
    public function SwitchControllers(
        object $class, 
        string $pages, 
        bool $switch = false, 
        string $views = ""
    ): mixed
    {
        $targetFunction = $this->transformToFunction($pages);
        $switch === false ?: $this->checkAutorisation($pages, $switch);
        
        $checkMethods = method_exists($class, $targetFunction);
        $checkDirectory = static::directory($pages, $switch , $views);
        
        return $checkMethods === true && $checkDirectory === true
            ? $class->$targetFunction($views.$pages)
            : static::class('errors')->error_404();
    }

    /**
     * @param object $class
     * @param mixed $pages
     * @return mixed
     */
    public function SwitchApiControllers(
        object $class, 
        string $pages
    ): mixed
    {
        $targetFunction = $this->transformToFunction($pages);

        return method_exists($class, $targetFunction)
            ? $class->$targetFunction()
            : $this->class('response')->JsonResponse(400, ['error' => "Method not found"]);
    }

    /**
     * @param string|null $html
     * @param bool|false $switch
     * @return bool
     */
    private static function directory(
        string|null $html = null, 
        bool $switch = false , 
        string $views = ""
    ): bool
    {

        return $switch === false 
            ? file_exists(_DIR_VIEWS_ . $views . $html . _FRONT_) 
            : file_exists(_DIR_VIEWS_ . $views . $html . _FRONT_);
    }

    /**
     * @param string|null $target
     * @param null|bool $autorize
     * @return bool|null
     */
    private function checkAutorisation( 
        string $target, 
        bool $autorize
    ): bool|null
    {

        return (new SwitchersHeredia)->swicthPagesAutorisation($target, $autorize);
    }

    /**
     * CamelCase treatment process...
     * 
     * @param string $initPage
     * @return string
     */
    private function transformToFunction( 
        string $initPage
    ): string{

        $extension = _MAIN_EXTENSION_;

        $initPage = preg_replace("/$extension$/", '', $initPage);

        $parts = explode('_', $initPage);

        $camelCaseParts = array_map(function ($part) {
            return ucfirst($part);
        }, $parts);

        $camelCaseString = lcfirst(implode('', $camelCaseParts));

        $contract = explode('/', $camelCaseString);

        $parts = count($contract) > 1 ? $contract[1] : $contract[0];

        return $parts;
    }
}