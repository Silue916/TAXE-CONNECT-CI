<?php

 namespace Epaphrodites\epaphrodites\Console\Stubs;

 class stubChatbot{
    
    /**
     * @param string $jsonPath
     * @param string $userJsonPath
     * @param string $chatBotName
     * @param string $controller
     * @return void
     */
    public static function Generate(
        string $jsonPath, 
        string $userJsonPath, 
        string $chatBotName, 
        string $controller = "chats"
    ):void
    {
       
        $stubs = static::stubs($chatBotName);

        $jsonStubs = static::JsonContentModel($chatBotName);

        $FilesContent = file_get_contents($controller);
   
        $lastBracketPosition = strrpos($FilesContent, '}');

        if ($lastBracketPosition !== false) {
            $FilesContent = substr($FilesContent, 0, $lastBracketPosition);
        }  

        if ($lastBracketPosition !== false) {

            $FilesContent = substr_replace($FilesContent, $stubs."\n}", $lastBracketPosition);
            file_put_contents($controller, $FilesContent, LOCK_EX);
            file_put_contents($jsonPath, json_encode($jsonStubs,JSON_PRETTY_PRINT));
            file_put_contents($userJsonPath, json_encode([],JSON_PRETTY_PRINT));
        }
    } 
    
    private static function stubs($chatBotName){

        $functionName = static::transformToFunction($chatBotName);

        $stub = 
        "
    /**
    * start {$chatBotName} chatBot
    * 
    * @param string \$html
    * @return void
    */
    public final function {$functionName}Started(string \$html): void
    {

        \$chatBotName='$chatBotName';

        if (static::isValidMethod()) {

            \$send = static::isAjax('__send__') ? static::isAjax('__send__') : '';
    
            \$result = \$this->initNamespace()['bot']->herediaBotmodelOne(\$send , \$chatBotName);
    
            echo \$this->initNamespace()['ajax']->chatMessageContent(\$result , \$send , \$chatBotName);

            return;
        }

        \$this->views( \$html, [] , true);
    }";  
        
        return $stub;
    }

    /**
     * @param string $chatBotName
     * @return array
     */
    private static function JsonContentModel(
        string $chatBotName
    ):array
    {

        return [
            [
                "answers" => [
                    "I am {$chatBotName}, your AI assistant.",
                    "{$chatBotName} here, your AI technical support",
                    "Hi there, I'm {$chatBotName}, your personal AI tech helper"
                ],
                "key" => "hello morning hi {evening}",
                "similarly" => [
                    "hi",
                    "hello",
                    "morning",
                    "evening"
                ],
                "context" => "greeting",
                "name" => ["name"],
                "assembly" => [
                    "Delighted to see you {name}. How may I assist you?",
                    "Hello {name}, I am Epaphrodites, your AI technical assistant. How can I help you today?",
                    "Greetings {name}, I'm Epaphrodites, your AI technical assistant. How may I assist you?",
                    "Hello {name}, this is Epaphrodites, your AI technical support. How can I help you today?",
                    "Hi {name}, I'm Epaphrodites, your personal assistant in AI technology. How can I be of service to you?"
                ],
                "type" => "txt",
                "language" => "eng",
                "actions" => "none"
            ],
            [
                "answers" => [
                    "Je suis {$chatBotName}, votre assistant technique IA. Comment puis-je vous aider aujourd'hui ?",
                    "{$chatBotName} ici, prêt à vous aider en tant que support technique IA. Que puis-je faire pour vous ?",
                    "{$chatBotName} à votre service, votre support technique en IA. En quoi puis-je vous assister maintenant ?",
                ],
                "key" => "salut bonjour bonsoir",
                "context" => "salutation",
                "similarly" => [
                    "salut",
                    "bonjour",
                    "bonsoir"
                ],
                "name" => [],
                "assembly" => [],
                "type" => "txt",
                "language" => "fr",
                "actions" => "none"
            ],            
            [
                "answers" => [
                    "Hello, I am {$chatBotName}, your technical AI assistant. What can I do for you?",
                    "Greetings, I'm {$chatBotName}, your AI technical assistant. How may I assist you?"
                ],
                "key" => "clear",
                "context" => "initialiser",
                "similarly" => [
                    "clear"
                ],
                "name" => [],
                "assembly" => [],
                "type" => "txt",
                "language" => "eng",
                "actions" => "clear"
            ],
            [
                "answers" => [
                    "Bonjour, je suis {$chatBotName}, votre assistant technique en IA. Que puis-je faire pour vous ?",
                    "Salutations, je suis {$chatBotName}, votre assistant technique en IA. Comment puis-je vous aider ?"
                ],
                "key" => "init",
                "context" => "initialize",
                "similarly" => [
                    "init"
                ],
                "name" => [],
                "assembly" => [],
                "type" => "txt",
                "language" => "fr",
                "actions" => "clear"
            ],
            [
                "answers" => [
                    "Je peux comprendre et communiquer dans deux langues, notamment l'anglais et le français. Si tu veux, nous pouvons continuer cette conversation en français !",
                    "Je suis bilingue en anglais et en français. Si vous préférez, nous pouvons poursuivre notre conversation en français.",
                    "Je capable de comprendre et de m'exprimer dans les deux langues, anglais et français, je suis à votre disposition pour poursuivre cet échange en français si vous le désirez.",
                    "Bilingue et ouvert d'esprit, je suis heureux de poursuivre notre échange en français si cela vous arrange.",
                    "Je suis capable de naviguer entre l'anglais et le français avec aisance. Si vous le souhaitez, nous pouvons continuer notre conversation en français.",
                    "Anglais et français font partie de mon répertoire linguistique. N'hésitez pas à me parler en français si vous le souhaitez."
                ], 
                "key" => "utilise communique comprendre parle comprend exprime conversation converse continue [langue,francais,anglais]",
                "similarly" => [
                    "francais",
                    "anglais",
                    "langage"
                ],
                "name" => [],
                "context" => "langageCommunication",
                "assembly" => [],
                "type" => "txt",
                "language" => "fr",
                "actions" => "none"
            ],
            [
                "answers" => [
                    "I can understand and communicate in two languages, including English and French. If you'd like, we can continue this conversation in English!",
                    "I am bilingual in English and French. If you prefer, we can continue our conversation in English.",
                    "I am capable of understanding and expressing myself in both languages, English and French. I am at your disposal to continue this exchange in English if you wish.",
                    "Bilingual and open-minded, I am happy to continue our exchange in English if it suits you.",
                    "I am capable of navigating between English and French with ease. If you wish, we can continue our conversation in English.",
                    "English and French are part of my linguistic repertoire. Feel free to speak to me in English if you prefer."
                ], 
                "key" => "use using communicate fluent communicating understand speak speaking express continue [language,french,english]",
                "similarly" => [
                    "french",
                    "english",
                    "language"
                ],
                "name" => [],
                "context" => "communicateLanguage",
                "assembly" => [],
                "type" => "txt",
                "language" => "eng",
                "actions" => "none"
            ]                                      
        ];
    }

    /**
     *  @param string $initPage
     * @return string
     */
    private static function transformToFunction(
        string $initPage
    ): string
    {

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