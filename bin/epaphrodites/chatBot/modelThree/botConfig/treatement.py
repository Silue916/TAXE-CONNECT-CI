import json
import random
from fuzzywuzzy import fuzz
from constants import _LOAD_JSON_FILE_
from defaultAnswers import DefaultAnswers
from webHook import WebHook

class Treatement:
    
    @staticmethod
    def load_interactions():
        
        with open(_LOAD_JSON_FILE_, 'r') as file:
            return json.load(file)
    
    @staticmethod
    def find_interaction(interactions, question):
        
        question_words = question.lower().split()
        for interaction in interactions:
            if Treatement.check_essential_groups(interaction['essential'], question_words):
                if any(Treatement.match_word(key, question_words) for key in interaction['key']):
                    return interaction
        return None

    @staticmethod
    def check_essential_groups(essential_groups, question_words):

        for group in essential_groups:
            if all(Treatement.match_word(word, question_words) for word in group):
                return True
        return False

    @staticmethod
    def match_word(word, question_words):

        for q_word in question_words:
            if fuzz.partial_ratio(word.lower(), q_word.lower()) > 80:
                return True
        return False

    @staticmethod
    def response( question, lang, login ):
        
        jsonContent = Treatement.load_interactions()
        interaction = Treatement.find_interaction(jsonContent, question)
        
        if interaction:
            response = random.choice(interaction['response'])
            action_result = ""
            if interaction['action'] is not None:
                action_result = WebHook.listen(question, interaction['action'], interaction['check_in'], login)
            
            return response + action_result
        else:
            anwsers = DefaultAnswers.defaultAdditionalAnswers(lang)
            return anwsers