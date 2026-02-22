import sys
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
from datetime import datetime
from defaultMessages import DefaultMessages

class DateTimes:
    
    @staticmethod
    def getdate( lang='eng' )->str:
        now = datetime.now()
        
        if lang == 'fr':
            bot_date = now.strftime('%d %B %Y')
            return DefaultMessages.monthDate(bot_date)

        else:
            bot_date = now.strftime('%B %d, %Y')
        
        return bot_date
    
    @staticmethod
    def getHour()->str:
        now = datetime.now()
        bot_date = now.strftime('%H:%M:%S')
        return bot_date
