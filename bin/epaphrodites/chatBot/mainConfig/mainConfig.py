from difflib import SequenceMatcher

class MainConfig:
    
    @staticmethod
    def similarity(a, b):
        return SequenceMatcher(None, a, b).ratio()