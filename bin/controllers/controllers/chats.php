<?php

namespace Epaphrodites\controllers\controllers;

use Epaphrodites\controllers\switchers\MainSwitchers;

final class chats extends MainSwitchers
{
    private object $json;
    private object $data;
    private object $chatBot;
    private array|bool $result = [];
    private object $ajaxTemplate;

    /**
     * Initialize object properties when an instance is created
     * 
     * @return void
     */
    public final function __construct()
    {
        $this->initializeObjects();
    }    

   /**
     * Initialize each property using values retrieved from static configurations
     * 
     * @return void
     */
    private function initializeObjects(): void
    {
        $this->json = $this->getObject(static::$initNamespace, 'json');
        $this->data = $this->getObject(static::$initNamespace, 'datas');
        $this->chatBot = $this->getObject( static::$initNamespace , 'bot');
        $this->ajaxTemplate = $this->getObject( static::$initNamespace , "ajax");
    }  

    /**
     * This chatbot requires that php be installed
     * Start Epaphrodites Chatbot one
     *
     * @param string $html
     * @return void
     */
    public final function startChatbotModelOne(
        string $html
    ): void
    {

        if (static::isValidMethod(true)) {

            $send = static::isAjax('__send__') ? static::isAjax('__send__') : '';

            $this->result = $this->chatBot->chatBotmodelOneProcess($send);

            echo $this->ajaxTemplate->chatMessageContent($this->result , $send);
           
            return;
        }
     
        $this->views( $html, [], true );
    }  
    
    /**
     * This chatbot requires that Python be installed
     * Start Epaphrodites Chatbot two
     * @param string $html
     * @return void
     */
    public final function startChatbotModelTwo(
        string $html
    ): void
    {

        if (static::isValidMethod(true)) {
            
            $send = static::isAjax('__send__') ? static::isAjax('__send__') : '';

            $this->result = $this->chatBot->chatBotModelTwoProcess($send);

            echo $this->ajaxTemplate->chatMessageContent($this->result , $send);
           
            return;
        }
     
        $this->views( $html, [], true );
    }  
    
    /**
     * This chatbot requires that model llama3:8b be installed
     * 
     * @param string $html
     * @return void
     */
    public final function startOllamaChatbot(
        string $html
    ): void
    {

        if (static::isValidMethod()) {
            
            // Save conversation
            if(static::isAjax('__prompt__')&&static::isAjax('__response__')){

                $prompts = static::isAjax('__prompt__');
                $responses = static::isAjax('__response__');

                $this->json->path( _DIR_JSON_DATAS_. '/ollama/archive.json')->add(
                            [
                                'prompt' => $prompts, 
                                'reponses' => $responses
                            ]);
            }

            // Get instructions
            if(static::isAjax('__lang__')&&static::isAjax('__botName__')){

                $lang = static::isAjax('__lang__');
                $responses = static::isAjax('__botName__');

               echo (string) $this->data->botInstructions( $lang, $responses );
            }

            return;
        }

        $this->views($html, [], true);
    }    

    /**
    * Start Epaphrodites recognition
    * @param string $html
    * @return void
    */
    public final function startBotWriting(
        string $html
    ): void{      

        $this->views( $html, [], true );
    }    
}