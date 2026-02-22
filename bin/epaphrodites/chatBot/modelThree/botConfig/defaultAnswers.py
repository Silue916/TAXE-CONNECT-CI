
import random

class DefaultAnswers:
    
    @staticmethod
    def defaultAdditionalAnswers(lang:str):
        
        messages = []
        if lang == 'fr':
            messages = [
                "Je n'arrive pas à comprendre votre préoccupation. Pouvez-vous être plus explicite s'il vous plaît?",
                "Pouvez-vous préciser davantage votre demande pour que je puisse mieux vous aider?",
                "Je ne suis pas sûr de saisir exactement votre problème. Pourriez-vous fournir plus de détails?",
                "Votre explication n'est pas très claire pour moi. Pouvez-vous reformuler ou donner un exemple?",
                "Je veux m'assurer de bien comprendre votre situation. Pouvez-vous expliquer un peu plus?",
                "Pourriez-vous clarifier ce que vous voulez dire par là?",
                "Il semble y avoir une ambiguïté dans ce que vous avez dit. Pouvez-vous être plus précis?",
                "Je suis désolé, mais j'ai besoin de plus d'informations pour comprendre votre point de vue.",
                "Pourriez-vous développer un peu plus votre pensée afin que je puisse mieux saisir votre préoccupation?",
                "Pouvez-vous m'en dire un peu plus pour que je puisse vous apporter une réponse adéquate?",
            ]

        else:
            messages = [
                "I don't understand your concern. Could you please be more explicit?",
                "Could you please clarify your request so I can assist you better?",
                "I'm not sure I fully understand your issue. Could you provide more details?",
                "Your explanation is not very clear to me. Could you rephrase or give an example?",
                "I want to make sure I understand your situation. Could you explain a bit more?",
                "Could you clarify what you mean by that?",
                "There seems to be some ambiguity in what you said. Could you be more specific?",
                "I'm sorry, but I need more information to understand your point of view.",
                "Could you elaborate a bit more on your thoughts so I can better understand your concern?",
                "Could you tell me a bit more so that I can give you an appropriate response?",
            ]

        return random.choice(messages)