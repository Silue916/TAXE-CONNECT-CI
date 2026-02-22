<?php

namespace Epaphrodites\epaphrodites\env\toml\saveLoad;

use ErrorException;

trait loadToml
{

    /**
     * Generates the path to the TOML file based on the given filename.
     * 
     * @param string|null $file Filename without extension
     * @return string Full path to the TOML file
     */
    private function ifIsReadable(?string $tomlFilePath = NULL): string
    {
        if ($tomlFilePath === null) {
            throw new \InvalidArgumentException('Filename cannot be null.');
        }

        if (!is_readable($tomlFilePath)) {
            throw new ErrorException(sprintf('File "%s" is not readable', $tomlFilePath));
        }

        if (!is_file($tomlFilePath)) {
            throw new ErrorException(sprintf('File "%s" does not exist.', $tomlFilePath));
        }

        return $tomlFilePath;
    }

    /**
     * Reads content from the TOML file.
     *
     * @param int|null $file Filename without extension
     * @return string|null Content of the TOML file
     */
    private function loadTomlFile($tomlFilePath): ?string
    {

        $fileName = $this->ifIsReadable($tomlFilePath);

        return file_exists($fileName) ? file_get_contents($fileName) : null;
    } 
    
    /**
     * Reads content from the TOML file.
     *
     * @param int|null $file Filename without extension
     * @return array|null Content of the TOML file
     */
    private function parsToml(string $file)
    {

        $fileName = $this->loadTomlFile($file);

        return file_exists($fileName) ? parse_ini_file($fileName, true) : NULL;
    }    
}