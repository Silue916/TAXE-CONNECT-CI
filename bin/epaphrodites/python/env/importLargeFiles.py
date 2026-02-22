import sys
import json
import pandas as pd
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class ImportLargeFiles:
    """
    Class for importing large CSV, Excel, and ODS files efficiently.
    """

    @staticmethod
    def import_csv_file_in_chunks(file_path, chunk_size=100000):
        
        """
        Read a large CSV file in chunks and process each chunk.
        Yields each chunk as a list of dictionaries.
        """
        try:
            for chunk in pd.read_csv(file_path, chunksize=chunk_size):
                yield chunk.to_dict(orient="records")
        except FileNotFoundError:
            raise FileNotFoundError(f"CSV file not found: {file_path}")
        except pd.errors.ParserError as e:
            raise ValueError(f"Error parsing CSV file: {e}")
        except Exception as e:
            raise ValueError(f"Unexpected error reading CSV file: {e}")

    @staticmethod
    def import_excel_file(file_path, engine='openpyxl', merge_sheets=False):
        """
        Read an Excel or ODS file and return all sheets as a list of dictionaries.
        If `merge_sheets` is True, all sheets will be merged into a single list.
        """
        try:
            data = pd.read_excel(file_path, sheet_name=None, engine=engine)
            if merge_sheets:
                # Merge all sheets into a single list of dictionaries
                merged_data = pd.concat(data.values(), ignore_index=True)
                return merged_data.to_dict(orient="records")
            else:
                # Return the first sheet's data
                first_sheet = next(iter(data.values()))
                return first_sheet.to_dict(orient="records")
        except FileNotFoundError:
            raise FileNotFoundError(f"Excel file not found: {file_path}")
        except ValueError as e:
            raise ValueError(f"Error reading Excel/ODS file with engine '{engine}': {e}")
        except Exception as e:
            raise ValueError(f"Unexpected error reading Excel/ODS file: {e}")


    @staticmethod
    def launch_import_files(file_path, file_extension):
        """
        Dispatch file import based on the file extension.
        """
        if file_extension == '.csv':
            return ImportLargeFiles.import_csv_file_in_chunks(file_path)
        elif file_extension in ['.xls', '.xlsx', '.ods']:
            engine = 'openpyxl' if file_extension in ['.xls', '.xlsx'] else 'odf'
            return ImportLargeFiles.import_excel_file(file_path, engine=engine)
        else:
            raise ValueError(f"Unsupported file type: {file_extension}")

def main():
    if len(sys.argv) != 2:
        print("Usage: python importLargeFiles.py <json_values>")
        sys.exit(1)

    json_values = sys.argv[1]
    
    try:
        json_data = InitJsonLoader.loadJsonValues(json_values, ',')
    except Exception as e:
        print(f"Failed to load JSON values: {e}")
        sys.exit(1)

    # Validate required JSON keys
    required_keys = ['function', 'excel', 'ext']
    if not all(key in json_data for key in required_keys):
        print(f"The JSON file must contain the following keys: {', '.join(required_keys)}.")
        sys.exit(1)

    json_function = json_data['function']
    if json_function != "getExcelContent":
        print(f"The function '{json_function}' is not recognized.")
        sys.exit(1)

    excel_path_file = json_data.get("excel")
    excel_file_ext = json_data.get("ext")
    try:
        imported_data = ImportLargeFiles.launch_import_files(excel_path_file, excel_file_ext)
        
        if excel_file_ext == '.csv':
            
            for chunk in imported_data:
                print(json.dumps(chunk, ensure_ascii=False, indent=4))  # Print each chunk for CSV files
        else:
            
            print(json.dumps(imported_data, ensure_ascii=False, indent=4))  # Print all sheets for Excel/ODS files
    except Exception as e:
        print(f"Error during file import: {e}")
        sys.exit(1)


if __name__ == '__main__':
    main()
