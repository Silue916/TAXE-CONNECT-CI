import sys
import logging
from pathlib import Path
import PyPDF2
from typing import Optional, Dict
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class PDFConversionError(Exception):
    """Custom exception for PDF conversion errors"""
    pass

class TranslateDocumentToText:
    
    @staticmethod
    def pdf_to_text(file_path: str, password: Optional[str] = None) -> str:

        try:
            file_path = Path(file_path)
            if not file_path.exists():
                raise PDFConversionError(f"The file '{file_path}' does not exist.")
                
            with open(file_path, 'rb') as file:
                reader = PyPDF2.PdfReader(file)
                
                if reader.is_encrypted and password:
                    reader.decrypt(password)
                    
                text = []
                for page_num, page in enumerate(reader.pages, 1):
                    try:
                        text.append(page.extract_text())
                    except Exception as e:
                        logging.warning(f"Error extracting page {page_num}: {str(e)}")
                        
                return '\n'.join(text)
                
        except Exception as e:
            raise PDFConversionError(f"Error during PDF conversion: {str(e)}")

def validate_json_data(data: Dict) -> bool:

    required_fields = ['function', 'pdf']
    return all(field in data for field in required_fields)

def main():

    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s'
    )
    
    try:
        
        if len(sys.argv) != 2:
            raise ValueError("Usage: python translateDocumentToText.py <document_json_path>")
            
        json_values = sys.argv[1]
        document_data = InitJsonLoader.loadJsonValues(json_values, ',')
        
        if not validate_json_data(document_data):
            raise ValueError("The JSON file must contain 'function' and 'pdf'.")
            
        json_function = document_data['function']
        document_path = document_data['pdf']
        password = document_data.get('password')
        
        if json_function == "pdf_converter":
            converter = TranslateDocumentToText()
            extracted_text = converter.pdf_to_text(document_path, password)
            print(extracted_text)
        else:
            raise ValueError(f"The function '{json_function}' is not recognized.")
            
    except (ValueError, PDFConversionError) as e:
        logging.error(str(e))
        sys.exit(1)
    except Exception as e:
        logging.error(f"An unexpected error occurred: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()