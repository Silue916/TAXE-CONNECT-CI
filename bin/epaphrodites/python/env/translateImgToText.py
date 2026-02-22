import sys
import re
import pytesseract
from scipy import ndimage
import numpy as np
from PIL import Image, ImageFilter, ImageOps
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class TranslateImgToText:
    def __init__(self, img_path):
        self.img_path = img_path
        self.supported_langs = ['fra', 'eng', 'spa', 'deu', 'ita', 'por', 'rus', 'ara', 'hin', 'jpn', 'chi_sim', 'chi_tra']

    def preprocess_image(self, image):
        """Convert the image to grayscale, apply Gaussian blur, autocontrast, and binary closing."""
        gray = image.convert('L').filter(ImageFilter.GaussianBlur(radius=0.4))
        thresh = ImageOps.autocontrast(gray)
        binary_image = thresh.point(lambda x: 0 if x < thresh.getextrema()[1] / 2 else 255, '1')
        binary_image = ndimage.binary_closing(binary_image, structure=np.ones((2, 2)))
        return binary_image

    def detect_language(self, image):
        """Try to detect the language of the text in the image using OCR with orientation and script detection (OSD)."""
        try:
            detected_language = pytesseract.image_to_string(image, lang='osd').split('\n')[0].split(':')[1].strip()
            if detected_language not in self.supported_langs:
                detected_language = 'eng'  # Default to English if detection fails or unsupported language
        except (IndexError, AttributeError):
            detected_language = 'eng'
        return detected_language

    def extract_text(self, image, language):
        """Extract text from the image in the detected language, falling back to other languages if extraction is weak."""
        extracted_text = pytesseract.image_to_string(image, lang=language).strip()
        
        # If text extraction is insufficient, try other languages
        if len(extracted_text) < 10:
            for lang in self.supported_langs:
                if lang != language:
                    additional_text = pytesseract.image_to_string(image, lang=lang).strip()
                    extracted_text += ' ' + additional_text if additional_text else ''
        
        return extracted_text

    def get_image_content(self):
        """Main method to load the image, process it, detect language, and extract text."""
        try:
            image = Image.open(self.img_path)
            preprocessed_image = self.preprocess_image(image)
            language = self.detect_language(preprocessed_image)
            extracted_text = self.extract_text(preprocessed_image, language)
            cleaned_text = self.post_process(extracted_text)
            return cleaned_text
        except Exception as e:
            return f"Error when extracting the image text: {str(e)}"

    @staticmethod
    def post_process(text):
        """Clean up the extracted text by removing unwanted characters and normalizing it."""
        text = re.sub(r'\n\s*\n', '\n', text)  # Remove excessive newlines
        text = re.sub(r'[^\w\s\u00C0-\u017F\/\'\",;:!)(+@?.-]', '', text)  # Allow only common punctuations and alphanumerics
        text = re.sub(r'([\\/\'\",;:!?_&%@|><.#*}{-])\1+', r'\1', text)  # Remove repeating special characters
        text = re.sub(r'\s+', ' ', text).strip()  # Normalize white spaces
        return text

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print("Usage: python translateImgTotext.py <json_values>")
        sys.exit(1)

    json_values = sys.argv[1]
    json_datas = InitJsonLoader.loadJsonValues(json_values, ',')

    if 'function' not in json_datas or 'img' not in json_datas:
        print("The JSON file must contain 'function' and 'img'.")
        sys.exit(1)

    json_function = json_datas['function']
    if json_function == "getImgContent":
        img_path = json_datas.get("img")
        image_processor = TranslateImgToText(img_path)
        text_extract = image_processor.get_image_content()
        print(text_extract)
    else:
        print(f"The function '{json_function}' is not recognized.")
        sys.exit(1)
