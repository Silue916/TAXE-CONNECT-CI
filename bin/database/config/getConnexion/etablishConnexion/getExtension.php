<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;


trait getExtension{

    /**
     * Checks if a PHP extension is loaded.
     *
     * @param string $extensionName The name of the extension to check.
     * @throws \InvalidArgumentException If the extension name is empty or invalid.
     * @throws \RuntimeException If the extension is not loaded.
     */
    protected function ifExtensionExist(
        string $extensionName
    ): void{
        if (empty(trim($extensionName))) {
            throw new \InvalidArgumentException('The extension name cannot be empty.');
        }

        if (!extension_loaded($extensionName)) {
            throw new \RuntimeException(sprintf('The PHP extension "%s" is not loaded.', $extensionName));
        }
    }  
}