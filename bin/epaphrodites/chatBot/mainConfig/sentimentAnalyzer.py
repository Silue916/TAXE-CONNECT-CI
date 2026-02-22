import sys
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
from defaultMessages import DefaultMessages

class SentimentAnayser :
    
    @staticmethod
    def treatement(sentence, language='fr'):
        
        words = sentence.lower().split()
        vocabulary = DefaultMessages.vocabulary(language)

        scores = {
            'auxiliary_verbs': 0,
            'neutral_words': 0,
            'negative_words': 0,
            'negations': 0,
            'attenuators': 0,
            'intensifiers': 0
        }

        for word in words:
            scores['auxiliary_verbs'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('auxiliaryVerbs', []))
            scores['neutral_words'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('neutralWords', []))
            scores['negative_words'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('negativeWords', []))
            scores['negations'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('negations', []) + vocabulary.get('endNegations', []))
            scores['attenuators'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('attenuators', []))
            scores['intensifiers'] += SentimentAnayser.is_word_in_array(word, vocabulary.get('intensifiers', []))

        return SentimentAnayser.get_sentiment(scores,language)

    @staticmethod
    def is_word_in_array(word, array):
        return 1 if word in array else 0

    @staticmethod
    def get_sentiment(scores,lang):
        
        if (scores['negations'] == 0 and scores['negative_words'] == 0) or \
           (scores['negative_words'] != 0 and scores['auxiliary_verbs'] != 0 and scores['negations'] != 0) or \
           (scores['negative_words'] != 0 and scores['auxiliary_verbs'] == 0 and scores['negations'] != 0):
            return ''
        else:
            return DefaultMessages.apologizeSentences(lang)