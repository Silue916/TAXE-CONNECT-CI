from noella import Noella

class AdditionalAnswers:
    
    @staticmethod
    def defaultAdditionalAnswers(initMessage:str , lang:str):
        return [
            { 
                 "language": "fr", 
                 "question": [
                    "quelle est la date moment",
                    "quelle est la date aujourd",
                    "quelle date sommes nous",
                    "peux tu me dire la date",
                    "quel jour sommes nous",
                    "quel jour nous sommes",
                    "nous somme quel jour aujourd",
                    "donne moi la date",
                    "connaitre la date",
                    "peux tu me dire quelle jour nous sommes",
                    "peux tu me dire la date du jour",
                    "tu sais quel est le jour",
                    "dis moi le jour",
                    "je veux savoir la date",
                    "date il te plait",
                    "quelle est la date en ce moment",
                    "pourrais tu me dire la date actuelle",
                    "quel est le jour aujourd ?",
                    "quelle date est il",
                    "je voudrais connaitre la date"
                ], 
                "answers": 
                    [ 
                     f"Aujourd'hui est le {Noella.getdate(lang)}", 
                     f"La date actuelle est: {Noella.getdate(lang)}", 
                     f"Nous sommes aujourd'hui le {Noella.getdate(lang)}"
                    ] 
            },{
                "language": "eng", 
                "question": [
                    "what is the date at the moment",
                    "what is the date today",
                    "what date are we",
                    "can you tell me the date",
                    "what day are we",
                    "what day are we today",
                    "we are what day today",
                    "give me the date",
                    "know the date",
                    "can you tell me what day we are",
                    "can you tell me the date of the day",
                    "do you know what day it is",
                    "tell me the day",
                    "I want to know the date",
                    "date please",
                    "what is the date right now",
                    "could you tell me the current date",
                    "what day is today?",
                    "what date is it",
                    "I would like to know the date"
                ],
                "answers": [
                    f"Today is {Noella.getdate(lang)}",
                    f"The current date is: {Noella.getdate(lang)}",
                    f"We are today's date: {Noella.getdate(lang)}"
                ]               
            },{ 
                 "language": "fr", 
                 "question": [
                    "quelle est heure moment",
                    "quelle est heure maintenant",
                    "quelle heure est",
                    "donne moi heure",
                    "peux tu me dire heure",
                    "quel est le temps actuel",
                    "tu connais heure",
                    "dis moi heure",
                    "heures il te plait",
                    "quelle heure il est en ce moment",
                    "tu pourrais me donner heure",
                    "donne moi heure actuelle",
                    "peux tu me dire heure est",
                    "peux tu me dire heure fait",
                    "je veux savoir heure",
                    "peux-tu me dire heure actuelle",
                    "quelle est heure actuelle",
                    "quel est le moment de la journee",
                    "je voudrais connaitre heure",
                    "quel temps fait il"
                 ], 
                 "answers": 
                    [ 
                     f"Il est {Noella.getHour()}", 
                     f"Il est actuellement {Noella.getHour()}", 
                     f"L'heure actuelle est {Noella.getHour()}"
                    ] 
            },{
                "language": "eng",
                "question": [
                    "what is the time at the moment",
                    "what is the time now",
                    "what time is it",
                    "can you tell me the time",
                    "what is the current time",
                    "do you know the time",
                    "tell me the time",
                    "time please",
                    "what time is it right now",
                    "could you give me the time",
                    "give me the current time",
                    "can you tell me what time it is",
                    "can you tell me what time it is",
                    "I want to know the time",
                    "can you tell me the current time",
                    "what is the current time",
                    "what time of day is it",
                    "I would like to know the time",
                    "what's the weather like"
                ],
                "answers": [ 
                    f"It is {Noella.getHour()}", 
                    f"It is currently {Noella.getHour()}", 
                    f"The current time is {Noella.getHour()}"
                ]               
            }                                          
        ]