<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\defaultAnswers;

class mainHerediaDefaultMessages
{

    /**
     * @return array
     */
    public function defaultMessageInEnglishWhereNoAnswers():array
    {
        // Get bot default messages
        return [ 
            'answers' => [
                "I am a professional assistance AI. I do not handle this type of information.",
                "I am a language model and I cannot assist you with this question.",
                "I am designed to assist with technical tasks, but not this type of information.",
                "I am a language model specialized in technical assistance; however, I do not deal with this kind of information.",
                "I am a language model designed to assist with technical tasks, but I cannot help you with this type of information."
            ], 
            "similarly" => [],
            "type" => "txt",
            "name" => [],
            "context" => "",
            "assembly" => [],            
            "language" => "eng",
            "actions" => "none"
        ];
    }

    /**
     * @return array
     */
    public function defaultMessageInFrenchWhereNoAnswers():array
    {
        // Get bot default messages
        return [ 
            'answers' => [
                "Je suis une IA d'assistance professionnelle. Je ne gère pas ce type d'informations.",
                "Je suis un modèle de langage et je ne peux pas vous aider avec cette question.",
                "Je suis conçu pour aider avec des tâches techniques, mais pas pour ce type d'information.",
                "Je suis un modèle de langage spécialisé dans l'assistance technique, cependant, je ne traite pas ce genre d'information.",
                "Je suis un modèle de langage conçu pour aider avec des tâches techniques, mais je ne peux pas vous aider avec ce type d'informations."
            ], 
            "similarly" => [],
            "type" => "txt",
            "name" => [],
            "context" => "",
            "assembly" => [],            
            "language" => "fr",
            "actions" => "none"
        ];
    }   
    
    /**
     * @return array
     */    
    public function defaultMessageInEnglishToGetMorePrecision():array
    {
        // Get bot default messages
        return [ 
            'answers' => [
                "I don't understand your concern. Could you please be more explicit?",
                "Your concern is not clear to me. Could you provide more details, please?",
                "To ensure accuracy, could you provide more details, please?",
                "So that I can effectively address your concern, could you elaborate on your explanations?",
                "I'm here to help you, but I need a bit more clarity. Could you detail your concern?"
            ], 
            "similarly" => [],
            "type" => "txt",
            "name" => [],
            "context" => "",
            "assembly" => [],            
            "language" => "eng",
            "actions" => "none"
        ];
    } 

    /**
     * @return array
     */    
    public function defaultMessageInFrenchToGetMorePrecision():array
    {
        // Get bot default messages
        return [ 
            'answers' => [
                "Je ne comprends pas votre préoccupation. Pourriez-vous être plus explicite, s'il vous plaît ?",
                "Votre préoccupation n'est pas claire pour moi. Pourriez-vous fournir plus de détails, s'il vous plaît ?",
                "Pour garantir la précision, pourriez-vous fournir plus de détails, s'il vous plaît ?",
                "Pour que je puisse répondre efficacement à votre préoccupation, pourriez-vous étoffer vos explications ?",
                "Je suis là pour vous aider, mais j'ai besoin d'un peu plus de clarté. Pourriez-vous détailler votre préoccupation ?"
            ], 
            "similarly" => [],
            "type" => "txt",
            "name" => [],
            "context" => "",
            "assembly" => [],                        
            "language" => "fr",
            "actions" => "none"
        ];
    }   
}