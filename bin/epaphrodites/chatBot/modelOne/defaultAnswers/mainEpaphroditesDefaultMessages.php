<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\defaultAnswers;

use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\randomArray;

class mainEpaphroditesDefaultMessages
{

    use randomArray;

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
            ], 
            "similarly" => [],
            "name" => [],
            "type" => "txt",
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
                "Je suis une IA d'assistance. Je ne gère pas ce type d'informations.",
                "Je suis un modèle de langage et je ne peux pas vous aider avec cette question."
            ], 
            "similarly" => [],
            "name" => [],
            "type" => "txt",
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
    
    /**
     * @param string $language
     * @return array
    */
    public function getVocabulary(
        string $language
    ): array {

        $vocabulary = [
            'fr' => [
                'negations' => ['ne', 'ni', 'non', 'ne pas', 'pas'],
                'attenuators' => ['peu', 'legerement', 'assez', 'relativement', 'moderement', 'plutot'],
                'endNegations' => ['pas', 'jamais', 'rien', 'aucun', 'aucune', 'nullement', 'guere', 'nul', 'nulle', 'personne'],
                'intensifiers' => ['tres', 'extremement', 'particulierement', 'completement', 'totalement', 'terriblement', 'absolument', 'vraiment', 'fortement', 'profondement', 'grandement', 'enormement'],
                'neutralWords' => ['satisfait', 'content', 'heureux', 'ravi', 'adore', 'apprecie', 'recommande', 'correcte', 'felicite', 'prefere', 'admire', 'aime', 'approuve', 'plaît', 'satisfaisant'],
                'negativeWords' => ['mecontent', 'decu', 'frustre', 'frustrant', 'insatisfait', 'probleme', 'pire', 'critique', 'bonne', 'incorrecte', 'deprime', 'triste', 'decourage', 'decourageant', 'atroce', 'insupportable', 'effroyable', 'abominable', 'desesperant', 'affreux', 'epouvantable', 'horrible', 'desastreux', 'desolant', 'lamentable', 'mauvais', 'mediocre', 'nul', 'terrible'],
                'auxiliaryVerbs' => ['suis', 'es', 'est', 'sommes', 'etes', 'sont', 'ai', 'as', 'a', 'avons', 'avez', 'ont', 'serai', 'seras', 'sera', 'serons', 'serez', 'seront', 'serais', 'serait', 'serions', 'seriez', 'seraient']
            ],
            'eng' => [
                'negations' => ['not', 'no', 'never', 'neither', 'nor', 'none', 'nowhere', 'nothing', 'hardly', 'scarcely', 'barely', 'doesn\'t', 'isn\'t', 'wasn\'t', 'shouldn\'t', 'wouldn\'t', 'couldn\'t', 'won\'t', 'can\'t', 'don\'t'],
                'attenuators' => ['slightly', 'somewhat', 'relatively', 'moderately', 'quite', 'rather', 'fairly', 'pretty'],
                'endNegations' => ['not', 'never', 'nothing', 'no', 'none', 'neither', 'nor', 'nobody', 'nowhere', 'hardly', 'scarcely', 'barely'],
                'intensifiers' => ['very', 'extremely', 'particularly', 'completely', 'totally', 'terribly', 'absolutely', 'really', 'strongly', 'deeply', 'greatly', 'enormously', 'exceptionally', 'incredibly', 'immensely'],
                'neutralWords' => ['satisfied', 'pleased', 'happy', 'delighted', 'love', 'appreciate', 'recommend', 'correct', 'congratulate', 'prefer', 'admire', 'like', 'approve', 'enjoy', 'satisfying'],
                'negativeWords' => ['dissatisfied', 'disappointed', 'frustrated', 'frustrating', 'unsatisfied', 'problem', 'worst', 'critical', 'incorrect', 'depressed', 'sad', 'discouraged', 'discouraging', 'atrocious', 'unbearable', 'dreadful', 'abominable', 'hopeless', 'awful', 'terrible', 'horrible', 'disastrous', 'deplorable', 'lamentable', 'bad', 'poor', 'worthless', 'lousy'],
                'auxiliaryVerbs' => ['am', 'is', 'are', 'was', 'were', 'been', 'being', 'have', 'has', 'had', 'having', 'will', 'would', 'shall', 'should', 'may', 'might', 'must', 'can', 'could', 'do', 'does', 'did', 'done', 'doing']
            ]
        ];
       
        return $vocabulary[$language] ?? [];
    }    

    /**
     * @param string $lang
     * @return string
     */
    public function apologizeSentences(
        string $lang
    ):string{
        
        $msg = [
            'fr' =>[
                "Je comprends, et je m'excuse si mes réponses ne répondent pas à vos attentes.\n",
                "Je comprends votre point de vue et je m'excuse si mes réponses ne sont pas tout à fait ce que vous attendiez.\n",
                "Je prends note de votre perspective et je m'excuse si mes réponses n'ont pas été à la hauteur de ce que vous espériez.\n",
                "Je comprends votre position et je m'excuse si mes réponses n'ont pas pleinement répondu à vos attentes.\n"
            ],
            'eng' =>[
                "I understand, and I apologize if my responses do not meet your expectations.\n",
                "I understand your point of view, and I apologize if my responses are not quite what you expected.\n",
                "I take note of your perspective, and I apologize if my responses have not lived up to what you hoped for.\n",
                "I understand your position, and I apologize if my responses have not fully met your expectations.\n"
            ]            
        ];

        return $this->answersChanging($msg[$lang]) ?? [];
    }    
}