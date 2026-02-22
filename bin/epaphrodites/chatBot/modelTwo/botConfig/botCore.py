import sys
import random
from datetime import datetime
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
from difflib import get_close_matches as closeMatches
from loadAndSave import LoadAndSave
from normalizedWords import NormalizedWords
from detectLanguages import DetectLanguages
from defaultMessages import DefaultMessages
from additionalAnswers import AdditionalAnswers
from sentimentAnalyzer import SentimentAnayser
from constants import _LOAD_JSON_FILE_, _SAVE_TO_JSON_FILE_

class BotCore:
            
    @staticmethod
    def findBestMacth(user_question: str, questions: list[str]) -> str | None:
        matches = closeMatches(user_question, questions, n=1, cutoff=0.8)
        return matches[0] if matches else None
    
    @staticmethod
    def getAnswerForQuestions(question: str, knowledge_base: dict) -> str | None:
        for q in knowledge_base:
            if question in q["question"]:
                return random.choice(q["answers"])          
            
    @staticmethod
    def onlyBotDiscutionTreatment(login, initmessages):
        now = datetime.now()
        botDate = now.strftime('%d-%m-%Y %H:%M:%S')
        
        messages = NormalizedWords.normalizeUsersMessages(initmessages) 
        
        lang = DetectLanguages.detect_language_with_dictionary(messages, login)
        
        ResponseToUsersSentiment = SentimentAnayser.treatement(initmessages, lang)
        
        defaultResponseMessages = DefaultMessages.botDefaultAnswers(lang)
        
        defaultAdditionalAnswers = AdditionalAnswers.defaultAdditionalAnswers(messages, lang)
        
        init_knowledge_base = LoadAndSave.load_knowledge_base(_LOAD_JSON_FILE_)
        
        knowledge_base = init_knowledge_base + defaultAdditionalAnswers
        
        best_match = BotCore.findBestMacth(messages, [q for sublist in [q["question"] for q in knowledge_base] for q in sublist])    
        
        if best_match:
            
            return {'date':botDate,'language': lang,'question': messages,'answers': BotCore.getAnswerForQuestions(best_match, knowledge_base) , 'login': login, 'state': False}
        else:
            return {'date':botDate,'language': lang,'question': messages,'answers': ResponseToUsersSentiment + defaultResponseMessages , 'login': login, 'state': True}  
        
        
    def botLearnAndDiscutionTreatment(login, initMessages):
        
        now = datetime.now()
        botDate = now.strftime('%d-%m-%Y %H:%M:%S')
        
        messages = NormalizedWords.normalizeUsersMessages(initMessages)
        
        lang = DetectLanguages.detect_language_with_dictionary(messages,login)
        
        defaultInitResponseMessages = DefaultMessages.defaultInitMessages(lang)
        
        ResponseToUsersSentiment = SentimentAnayser.treatement(initMessages, lang)
        
        defaultAdditionalAnswers = AdditionalAnswers.defaultAdditionalAnswers(initMessages, lang)
        
        init_knowledge_base = LoadAndSave.load_knowledge_base(_LOAD_JSON_FILE_)
        
        knowledge_base = init_knowledge_base + defaultAdditionalAnswers
        
        best_match = BotCore.findBestMacth(messages, [q for sublist in [q["question"] for q in knowledge_base] for q in sublist])
        
        if best_match:

            return {'date':botDate,'language': lang,'question': messages,'answers': ResponseToUsersSentiment + BotCore.getAnswerForQuestions(best_match, knowledge_base) , 'login': login, 'state': False}
        else:
            
            defaultThankResponseMessages = DefaultMessages.defaultThankMessages(lang)
            
            defaultTeachResponseMessages = DefaultMessages.defaultTeachMessages(lang)
            
            getLastMessage = LoadAndSave.load_knowledge_base(_SAVE_TO_JSON_FILE_)
            
            lastConversation = LoadAndSave.get_last_learn_datas(getLastMessage , login)

            if isinstance(lastConversation, dict):
                
                if messages != 'stop':
                    
                    if lastConversation['state'] == True :
                        
                        init_knowledge_base.append({'language': lang,"question": lastConversation['question'].split("|"), "answers": initMessages.split("|")})
                        
                        LoadAndSave.save_knowledge_base(_LOAD_JSON_FILE_, init_knowledge_base)
                        
                        return {'date':botDate,'language': lang,'question': messages, 'answers': defaultThankResponseMessages, 'login': login, 'state': False}        
                else:
                    return {'date':botDate,'language': lang,'question': messages,'answers': defaultInitResponseMessages , 'login': login, 'state': False} 
                            
            return {'date':botDate,'language': lang,'question': messages,'answers': ResponseToUsersSentiment + defaultTeachResponseMessages , 'login': login, 'state': True}      