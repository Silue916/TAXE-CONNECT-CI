import sys
from googletrans import Translator, LANGUAGES

sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class TranslateWords:
    @staticmethod
    def get_language_abbreviations():
        return list(LANGUAGES.keys())

    @staticmethod
    def is_valid_abbreviation(abbr):
        return abbr in LANGUAGES

    @staticmethod
    def translate_text(text, target_language):
        if not TranslateWords.is_valid_abbreviation(target_language):
            raise ValueError(f"The language abbreviation '{target_language}' is not valid.")

        translator = Translator()
        translation = translator.translate(text, dest=target_language)
        return translation.text

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python translateWords.py <json_values>")
        sys.exit(1)

    json_values = sys.argv[1]
    json_datas = InitJsonLoader.loadJsonValues(json_values, ',')

    if 'text' not in json_datas or 'lang' not in json_datas:
        print("The JSON file must contain 'text' and 'lang'.")
        sys.exit(1)

    try:
        result = TranslateWords.translate_text(json_datas['text'], json_datas['lang'])
        print(result)
        
    except ValueError as e:
        print(str(e))
        sys.exit(1)