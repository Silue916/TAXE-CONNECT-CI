<?php

namespace Epaphrodites\epaphrodites\env\toml\saveLoad;

trait saveTomlDatas
{

    /**
     * Writes content to the TOML file.
     *
     * @param int|null $path Filename without extension
     * @param string $content Content to write to the file
     * @return bool
     */
    private function saveToml(
        string $path, 
        string $content
    ): bool{

        file_exists($path) ? file_put_contents($path, $content) : NULL;
        
        return true;
    }  
}