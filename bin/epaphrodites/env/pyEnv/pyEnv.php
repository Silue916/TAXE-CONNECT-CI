<?php

namespace Epaphrodites\epaphrodites\env\pyEnv;

trait pyEnv{

    /**
     * To use this function, you must install python 3
     * and run this command "pip install pycryptodome"
     * @param null|string $value
     * @param null|string $type
     * @return mixed
     */
    public function pyEncryptDecrypt(
        string $value, 
        string $type
    ):mixed
    {

        return static::initConfig()['python']->executePython($type, ['value' => $value]);
    }

    /**
     * To use this function, you must install python 3
     * and run this command "pip install pytesseract"
     * @param string $key
     * @return mixed
     */
    public function pyConvertImgToText(
        string $key
    ){
        $imgPath = $_FILES[$key]['tmp_name'];

        if (!file_exists($imgPath)) {
            throw new \Exception("Image paths are not valid.");
        }
        
        return static::initConfig()['python']->executePython('convertImgToText', ["img" => $imgPath]);
    }

    /**
     * To use this function, you must install python 3
     * and run this commande "pip install PyPDF2"
     * @param string $key
     * @return mixed
     */
    public function pyConvertPdfToText(
        string $key
    ){

        $pdfPath = $_FILES[$key]['tmp_name'];

        if (!file_exists($pdfPath)) {
            throw new \Exception("Document paths are not valid.");
        }

        return static::initConfig()['python']->executePython('convert_pdf_to_text', ["pdf" => $pdfPath]);
    } 
    
    /**
     * To use this function, you must install python 3
     * and run this commande "pip install googletrans==4.0.0-rc1"
     * @param mixed $text
     * @param string $lang
     * @return mixed
     */
    public function languageTranslator(
        string $text, 
        string $lang
    ){

        if (empty($text)&&empty($lang)) {
            throw new \Exception("verify your content text ans abrevation language");
        }

        return static::initConfig()['python']->executePython('translatWords', ["text" => $text , "lang"=>$lang ]);
    } 
    
    public function request(
        string $endpoint, 
        array $data = [], 
        string $method = 'GET', 
        array $options = []
    ): array{
     
        return static::initConfig()['python']->pyApi( $endpoint, $data, $method , $options );
    }
}