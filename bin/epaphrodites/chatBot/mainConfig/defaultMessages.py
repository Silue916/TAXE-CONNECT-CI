import random

class DefaultMessages:
    
    @staticmethod
    def defaultThankMessages(lang:str)->str:
        messages =[]
        if lang == 'fr':
            messages = [
                "Merci ! J'ai appris de nouvelles réponses !",
                "Merci ! J'ai acquis de nouvelles connaissances !",
                "Merci ! Je me suis enrichi de nouvelles réponses !",
                "Merci ! De nouvelles informations ont enrichi mes connaissances !"
            ]
        else:
            messages = [
                "Thank you! I learned new answers!",
                "Thank you! I've acquired new knowledge!",
                "Thank you! I have enriched myself with new answers!",
                "Thank you! New information has enriched my knowledge!"
            ]
            
        return random.choice(messages)
    
    
    @staticmethod
    def defaultTeachMessages(lang:str)->str:
        messages =[]
        if lang == 'fr':
            messages = [
                "Je suis désolé, mais je ne connais pas la réponse. Pourriez-vous m'instruire ? Si oui, veuillez saisir uniquement les réponses, en les séparant par un '|'. Sinon, écrivez 'stop' pour continuer.",
                "Navré, je ne détiens pas la réponse à cette question. Serait-il possible de m'éclairer ? Si c'est le cas, merci d'entrer les réponses en les distinguant par le symbole '|'. Sinon, écrivez 'stop' pour continuer.",
                "Excusez-moi, mais je n'ai pas la réponse. Pouvez-vous m'apprendre ? Si oui, insérez les réponses en les séparant avec un '|'. Sinon, écrivez 'stop' pour continuer."
            ]
        else:
            messages = [
                "I'm sorry, but I do not know the answer. Could you teach me? If yes, please only enter the answers, separating them with a '|'. Otherwise, write 'stop' to continue.",
                "I'm sorry, I do not have the answer to this question. Would it be possible to enlighten me? If so, please enter the answers, distinguishing them by the symbol '|'. Otherwise, write 'stop' to continue.",
                "Excuse me, but I do not have the answer. Can you teach me? If yes, insert the answers separating them with a '|'. Otherwise, write 'stop' to continue."
            ]
            
        return random.choice(messages)
    
    
    @staticmethod
    def botDefaultAnswers(lang:str)->str:
        messages = []
        if lang == 'fr':
            messages = [
                "Je suis une IA d'assistance professionnelle. Je ne gère pas ce type d'informations.",
                "Je suis un modèle de langage et je ne peux pas vous aider avec cette question.",
            ]
        else:
            messages = [
                "I am a professional assistance AI. I do not handle this type of information.",
                "I am a language model and I cannot assist you with this question.",
            ]
            
        return random.choice(messages)
    
    
    @staticmethod
    def defaultInitMessages(lang:str)->str:
        messages = []
        if lang == 'fr':
            messages = [
                "Merci, nous pouvons poursuivre notre conversation.",
                "Je vous remercie, nous sommes prêts à continuer nos échanges.",
                "Merci, nous pouvons reprendre notre dialogue.",
            ]
        else:
            messages = [
                "Thank you, we can continue our conversation.",
                "I thank you, we are ready to continue our exchanges.",
                "Thank you, we can resume our dialogue.",
            ]

        return random.choice(messages)
    
    
    @staticmethod
    def monthDate(bot_date)->str:
        month_translations = {
            'January': 'Janvier',
            'February': 'Février',
            'March': 'Mars',
            'April': 'Avril',
            'May': 'Mai',
            'June': 'Juin',
            'July': 'Juillet',
            'August': 'Août',
            'September': 'Septembre',
            'October': 'Octobre',
            'November': 'Novembre',
            'December': 'Décembre'
        }
            
        for english_month, french_month in month_translations.items():
                bot_date = bot_date.replace(english_month, french_month)
                
        return bot_date
    
    
    @staticmethod
    def vocabulary(lang:str)->dict:
        vocabulary = {
            'fr': {
                'negations' : ['ne', 'ni', 'non', 'ne pas', 'pas'],
                'attenuators' : ['peu', 'legerement', 'assez', 'relativement', 'moderement', 'plutot'],
                'endNegations' : ['pas', 'jamais', 'rien', 'aucun', 'aucune', 'nullement', 'guere', 'nul', 'nulle', 'personne'],
                'intensifiers' : ['tres', 'extremement', 'particulierement', 'completement', 'totalement', 'terriblement', 'absolument', 'vraiment', 'fortement', 'profondément', 'grandement', 'énormément'],
                'neutralWords' : ['satisfait', 'content', 'heureux', 'ravi', 'adore', 'apprecie', 'recommande', 'correcte', 'felicite', 'prefere', 'admire', 'aime', 'approuve', 'plaît', 'satisfaisant'],
                'negativeWords' : ['mecontent', 'decu', 'frustre', 'frustrant', 'insatisfait', 'probleme', 'pire', 'critique', 'bonne', 'incorrecte', 'deprime', 'triste', 'decourage', 'decourageant', 'atroce', 'insupportable', 'effroyable', 'abominable', 'desesperant', 'affreux', 'epouvantable', 'horrible', 'desastreux', 'desolant', 'lamentable', 'mauvais', 'mediocre', 'nul', 'terrible'],
                'auxiliaryVerbs' : ['suis', 'es', 'est', 'sommes', 'etes', 'sont', 'ai', 'as', 'a', 'avons', 'avez', 'ont', 'serai', 'seras', 'sera', 'serons', 'serez', 'seront', 'serais', 'serait', 'serions', 'seriez', 'seraient']
            },
            'en': {
                'negations': ['not', 'no', 'never', 'neither', 'nor', 'none', 'nowhere', 'nothing', 'hardly', 'scarcely', 'barely', 'doesn\'t', 'isn\'t', 'wasn\'t', 'shouldn\'t', 'wouldn\'t', 'couldn\'t', 'won\'t', 'can\'t', 'don\'t'],
                'attenuators': ['slightly', 'somewhat', 'relatively', 'moderately', 'quite', 'rather', 'fairly', 'pretty'],
                'endNegations': ['not', 'never', 'nothing', 'no', 'none', 'neither', 'nor', 'nobody', 'nowhere', 'hardly', 'scarcely', 'barely'],
                'intensifiers': ['very', 'extremely', 'particularly', 'completely', 'totally', 'terribly', 'absolutely', 'really', 'strongly', 'deeply', 'greatly', 'enormously', 'exceptionally', 'incredibly', 'immensely'],
                'neutralWords': ['satisfied', 'pleased', 'happy', 'delighted', 'love', 'appreciate', 'recommend', 'correct', 'congratulate', 'prefer', 'admire', 'like', 'approve', 'enjoy', 'satisfying'],
                'negativeWords': ['dissatisfied', 'disappointed', 'frustrated', 'frustrating', 'unsatisfied', 'problem', 'worst', 'critical', 'incorrect', 'depressed', 'sad', 'discouraged', 'discouraging', 'atrocious', 'unbearable', 'dreadful', 'abominable', 'hopeless', 'awful', 'terrible', 'horrible', 'disastrous', 'deplorable', 'lamentable', 'bad', 'poor', 'worthless', 'lousy'],
                'auxiliaryVerbs': ['am', 'is', 'are', 'was', 'were', 'been', 'being', 'have', 'has', 'had', 'having', 'will', 'would', 'shall', 'should', 'may', 'might', 'must', 'can', 'could', 'do', 'does', 'did', 'done', 'doing']
            }
        }
        
        return vocabulary[lang]
    
    
    @staticmethod
    def apologizeSentences(lang:str)->str:
        
        messages = {
            'fr' :[
                "Je comprends, et je m'excuse si mes réponses ne répondent pas à vos attentes.\n",
                "Je comprends votre point de vue et je m'excuse si mes réponses ne sont pas tout à fait ce que vous attendiez.\n",
                "Je prends note de votre perspective et je m'excuse si mes réponses n'ont pas été à la hauteur de ce que vous espériez.\n",
                "Je comprends votre position et je m'excuse si mes réponses n'ont pas pleinement répondu à vos attentes.\n"
            ],
            'eng' :[
                "I understand, and I apologize if my responses do not meet your expectations.\n",
                "I understand your point of view, and I apologize if my responses are not quite what you expected.\n",
                "I take note of your perspective, and I apologize if my responses have not lived up to what you hoped for.\n",
                "I understand your position, and I apologize if my responses have not fully met your expectations.\n"
            ]          
        }
        
        return random.choice(messages[lang])