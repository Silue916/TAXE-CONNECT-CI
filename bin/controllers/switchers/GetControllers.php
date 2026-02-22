<?php

declare(strict_types=1);

namespace Epaphrodites\controllers\switchers;

use Epaphrodites\controllers\controllerMap\controllerMap;

class GetControllers extends ControllersSwitchers
{
    use controllerMap;

    /**
     * Return true controller
     *
     * @param null|array $provider
     * @param null|string $paths
     * @return void
     */
    private function getSwitchMainControllers(
        array $provider = [], 
        string|null $paths = null
    ): void{
        $controllerMap = (array) $this->controllerMap();

        foreach ($controllerMap as $controllerName => $method) {
            if (!is_array($method)) {
                continue;
            }
    
            [$controller, $methodName, $switcher, $findPath, $views] = $method + [null, null, false, null, null];
            
            if (static::getController($controllerName, $provider, $switcher)) {
                $paths = $this->getPath($paths, $findPath);
                $controllerInstance = $this;
                $arguments = [$controller, $paths ?? null, $switcher, $views];

                $controllerInstance?->$methodName(...$arguments);
                return;
            }
        }
    
        $this->SwitchControllers($this->mainController(), $paths, false, _DIR_MAIN_TEMP_);
    }

    /**
     * @param string $provider
     * @param string|null $findPath
     * @return string
     */
    private function getPath(
        string $provider, 
        string|null $findPath = null
    ):string {

        $segments = explode('/', $provider);

        $segments = array_filter($segments, function($segment) {
            return !empty($segment);
        });

        array_shift($segments);
    
        $path = "$findPath/".implode('/', $segments);
    
        return $path;
    }

    /**
     * @param array $provider
     * @param string $paths
     * @return void
     */
    public function SwitchMainControllers(
        array $provider = [], 
        string|null $paths = null
    ): void
    {
        $this->getSwitchMainControllers($provider, $paths);
    }    
}