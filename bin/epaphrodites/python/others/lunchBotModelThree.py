import sys
sys.path.append('bin/epaphrodites/python/config/')
sys.path.append('bin/epaphrodites/chatBot/modelThree/bots/')
from initJsonLoader import InitJsonLoader
from noellaBot import NoellaBot

class LunchBotModelThree:

    @staticmethod
    def func_lunchModelThree(login, message):
        
       result = NoellaBot.getUsersMessages(login, message)
       return result

if __name__ == '__main__':  
    
    json_values = sys.argv[1]
    
    json_datas = InitJsonLoader.loadJsonValues(json_values)
    
    if 'login' not in json_datas or 'userMessages' not in json_datas:
        print("The JSON file must contain 'login' and 'userMessages'.")
        sys.exit(1)  
    
    result = LunchBotModelThree.func_lunchModelThree(json_datas['login'], json_datas['userMessages'])
    
    print(result)           