<?php
namespace Epaphrodites\epaphrodites\env\toml\requests;

trait updToml
{
    private $set;

    /**
     * @param array $set
     * @return self
     */
    public function set(array $set): self
    {
        $this->set = $set;
        return $this;
    }

    /**
     * Update the TOML file with given data.
     *
     * @param array $datas
     * @return bool
     */
    public function update(array $datas = []): bool
    {

        if (!isset($this->set)) {
            return false;
        }  
        
        if (!isset($this->path)) {
            return false;
        }          

        $tomlFileDatas = $this->loadTomlFile($this->path);

        $datasFound = $this->translateToArray($tomlFileDatas);

        if (!empty($datas)) {
            $datasFound = $this->filterArrayDatas($datasFound, $datas);
        }

        if (empty($datasFound)) {
            return false;
        }

        if (!isset($this->section) && !empty($datasFound)) {
            $this->updateWithoutSection($datasFound);
        } elseif (isset($this->section) && !empty($datasFound[$this->section])) {
            $this->updateWithSection($datasFound[$this->section]);
        } else {
            return false;
        }

        $updatedTomlContent = $this->translateToToml($datasFound);
        
        $this->saveToml($this->path, $updatedTomlContent);

        return true;
    }
}