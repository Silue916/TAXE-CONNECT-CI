<?php

namespace Epaphrodites\epaphrodites\ErrorsExceptions;

use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;

trait ErrorHandler {

    use currentFunctionNamespaces;

    /**
     * setException
     * @param object $e
     * @return array{file: mixed, layouts: mixed, line: mixed, message: mixed, title: string, trace: mixed}
     */
    private static function setException(object $e): array {

        return [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'title'    => 'EPAPHRODITES | FATAL ERROR',
            'trace'   => $e->getTraceAsString(),
            'layouts' => static::initNamespace()['layout']->errors()
        ];
    }

    /**
     * Set Error param
     * 
     * @param object $e
     * @return array
     */
    public static function getErrors(object $e): array {

        return self::setException($e);
    }

    /**
     * Get error log view path
     * @return string
     */
    public static function setView():string{

        return 'errors/helper';
    }
}