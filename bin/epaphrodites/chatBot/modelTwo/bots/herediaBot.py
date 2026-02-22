import sys
sys.path.append('bin/epaphrodites/chatBot/modelTwo/toJson/')
sys.path.append('bin/epaphrodites/chatBot/modelTwo/botConfig/')
from botCore import BotCore
from loadAndSave import LoadAndSave
from constants import _SAVE_TO_JSON_FILE_

class HerediaBot:

    @staticmethod
    def getUsersMessages(login, messages, learn):
        
        if learn == True:
            answers = BotCore.botLearnAndDiscutionTreatment(login, messages)
        else:
            answers = BotCore.onlyBotDiscutionTreatment(login, messages)
        
        getLastMessage = LoadAndSave.load_knowledge_base(_SAVE_TO_JSON_FILE_)
        getLastMessage.append(answers)
        LoadAndSave.save_knowledge_base(_SAVE_TO_JSON_FILE_, getLastMessage)
        
        return True