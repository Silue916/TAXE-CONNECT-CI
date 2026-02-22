import sys
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
sys.path.append('bin/epaphrodites/chatBot/modelThree/botConfig/')
sys.path.append('bin/epaphrodites/chatBot/modelTwo/botConfig/')
from detectLanguages import DetectLanguages
from normalizedWords import NormalizedWords
from noellaBotCore import NoellaBotCore

class NoellaBot:
    
    @staticmethod
    def getUsersMessages(login, initMessage):
        
        normalizedMessage = NormalizedWords.normalizeUsersMessages(initMessage)
        
        lang = DetectLanguages.detect_language_with_dictionary(normalizedMessage, login)
        
        result = NoellaBotCore.listenUsersMessage(login, initMessage, normalizedMessage, lang)
        
        return result