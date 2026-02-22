<?php

namespace Epaphrodites\epaphrodites\env\toml\requests;

trait addToToml
{

    private $path;
    private $section;
    private string $mergeDatas = '';

    /**
     * @param string $path
     * @return self
     */
    public function path(
        string $path
    ):self{

        $this->path = $path;
        return $this;
    }

    /**
     * @param string $section
     * @return self
    */
    public function section(
        string $section
    ): self{

        $this->section = $section;
        return $this;
    } 
    
    /**
     * @param array $datas
     * @return bool
     */
    public function add(array $datas = []): bool
    {
        
        $tomlDatas = $this->loadTomlFile($this->path);
    
        $currentDatas = !empty($tomlDatas[$this->section]);
    
        if (!$currentDatas) {

            $content = '';
            $content .= "[$this->section]\n";
    
            foreach ($datas as $key => $value) {
                if (is_array($value)) {
                    $formattedValues = implode(',', array_map(function ($item) {
                        return '"' . addslashes($item) . '"';
                    }, $value));
                    $content .= "$key = [$formattedValues]\n";
                } else {
                    $content .= "$key = \"" . addslashes($value) . "\"\n";
                }
            }
    
            $content .= !empty($tomlDatas) ? "\n" . $this->mergeDatas : $this->mergeDatas;
    
            $tomlDatas .= "\n$content";

            $this->saveToml($this->path, $tomlDatas);
    
            return true;
        }
    
        return false;
    }
    
}