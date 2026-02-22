import sys
sys.path.append('bin/epaphrodites/chatBot/modelTwo/bots/')
sys.path.append('bin/epaphrodites/python/config/')
from herediaBot import HerediaBot
from initJsonLoader import InitJsonLoader

class LunchBotModelTwo:

    @staticmethod
    def func_lunchBotModelTwo(login, message, learn):
        
       result = HerediaBot.getUsersMessages(login, message, learn)
       return result

if __name__ == '__main__':  
    
    json_values = sys.argv[1]
    
    json_datas = InitJsonLoader.loadJsonValues(json_values)
    
    if 'login' not in json_datas or 'userMessages' not in json_datas or 'learn' not in json_datas:
        print("The JSON file must contain 'login', 'userMessages' and 'learn'.")
        sys.exit(1)    
    
    result = LunchBotModelTwo.func_lunchBotModelTwo(json_datas['login'], json_datas['userMessages'], json_datas['learn'])
    
    print(result)        