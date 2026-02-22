import sys
import random
from datetime import datetime
from difflib import get_close_matches
from treatement import Treatement
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
sys.path.append('bin/epaphrodites/chatBot/modelTwo/toJson/')
from loadAndSave import LoadAndSave
from constants import _SAVE_TO_JSON_FILE_

class NoellaBotCore:
    
    @staticmethod
    def find_best_match(user_question: str, questions: list[str]) -> str | None:
        matches = get_close_matches(user_question, questions, n=1, cutoff=0.8)
        return matches[0] if matches else None
    
    @staticmethod
    def get_answer_for_question(question: str, knowledge_base: dict) -> str | None:
        for q in knowledge_base:
            if question in q["question"]:
                return random.choice(q["answers"])       
    
    @staticmethod
    def listenUsersMessage(login, initMessage, normalizedMessage, lang):
        
        now = datetime.now()
        
        botDate = now.strftime('%d-%m-%Y %H:%M:%S')
        
        response =  Treatement.response(normalizedMessage, lang, login)
        
        init_knowledge_base = LoadAndSave.load_knowledge_base(_SAVE_TO_JSON_FILE_)
        
        init_knowledge_base.append({'date':botDate, 'language': lang,"question": initMessage, "answers": response, 'login': login})
        
        LoadAndSave.save_knowledge_base(_SAVE_TO_JSON_FILE_, init_knowledge_base)
        
        return response