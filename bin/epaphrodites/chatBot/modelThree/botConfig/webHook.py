import sys
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
from dateTimes import DateTimes

class WebHook:
    
    @staticmethod
    def listen( question, actionName, checkInSentences, login ):
        
        actions = {
            'generate_controller': '',
            'get_date': DateTimes.getdate(),
            'get_hour': DateTimes.getHour()
        }
        
        lunchFunction = actions.get(actionName)
        
        if lunchFunction:
            return lunchFunction