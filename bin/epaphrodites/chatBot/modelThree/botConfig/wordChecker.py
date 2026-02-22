import re
from difflib import SequenceMatcher

class WordChecker:
    
    @staticmethod
    def similarities(word1, word2):
        return SequenceMatcher(None, word1.lower(), word2.lower()).ratio()
    
    @staticmethod
    def similaryKeyWord(word , keyWords, similraty = 0.8):
        for word_cle in keyWords:
            if WordChecker.similarities(word, word_cle) >= similraty:
                return word_cle
        return None    
    
    @staticmethod
    def findKeyWords(phrase:str , keyWords:list, ignoredWords:list, mainKey:str, othersKeys:list):

        words = re.findall(r'\w+', phrase)

        for i, word in enumerate(words):
            if word.lower() in ignoredWords:
                continue

            word_cle = WordChecker.similaryKeyWord(word, keyWords)
            
            if word_cle == mainKey:
                for j in range(i + 1, len(words)):
                    if words[j].lower() not in ignoredWords and words[j].istitle():
                        return words[j]
                for j in range(i - 1, -1, -1):
                    if words[j].lower() not in ignoredWords and words[j].istitle():
                        return words[j]
            elif word_cle in othersKeys:
                for j in range(i + 1, len(words)):
                    if words[j].lower() not in ignoredWords and words[j].istitle():
                        return words[j]

        for word in words:
            if word.lower() not in ignoredWords and word.istitle():
                return word

        return None