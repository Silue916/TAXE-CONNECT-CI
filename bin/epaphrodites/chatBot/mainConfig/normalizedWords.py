import unicodedata as uni
import re

class NormalizedWords:
    
    @staticmethod
    def normalizeUsersMessages(usersMessages):
        
        messagesToLowers = usersMessages.lower()
        
        wordNormalize = ''.join(c for c in uni.normalize('NFD', messagesToLowers) if uni.category(c) != 'Mn')
        
        wordNormalize = NormalizedWords.replaceCharacteres(wordNormalize)
        
        cleanSpace = wordNormalize.split()
        
        cleanSpace = " ".join(cleanSpace)
        
        return cleanSpace
    
    @staticmethod
    def replaceCharacteres(usersMessages):

        string = usersMessages
        
        charactersToReplace = ['-', '_', '+', '=', '.', "'", '/', '\\', ',', '!', '?', '(', ')', '"','{', '}', '[', ']', '&', '*', '%', "¸"]
        
        for caractere in charactersToReplace:
            string = string.replace(caractere, " ")
        
        string = re.sub(r'\s+', ' ', string)   
         
        return string