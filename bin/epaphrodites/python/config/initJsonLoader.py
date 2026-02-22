import json
import ast
import base64

class InitJsonLoader:

   @staticmethod
   def parse_custom_string_correctly(input_string):

        trimmed_str = input_string.strip('{} ')
        
        data = {}
        key = ''
        value = ''
        reading_key = True
        char_buffer = ''
        inside_quotes = False
        
        for char in trimmed_str:

            if char == '"' and not inside_quotes:
                inside_quotes = True
                char_buffer += char
                continue
            elif char == '"' and inside_quotes:
                inside_quotes = False
                char_buffer += char
                continue
            
            if reading_key:
                if char == ':' and not inside_quotes:

                    key = char_buffer.strip().strip('"')
                    char_buffer = ''
                    reading_key = False
                else:
                    char_buffer += char
            
            else:
                if char == ',' and not inside_quotes:
                   
                    value = char_buffer.strip()
                    
                    try:

                        parsed_value = ast.literal_eval(value)
                        data[key] = parsed_value
                    except (ValueError, SyntaxError):
                       
                        if value.startswith('"') and value.endswith('"'):
                            data[key] = value.strip('"')
                        elif value == 'true':
                            data[key] = True
                        elif value == 'false':
                            data[key] = False
                        elif value == 'null':
                            data[key] = None
                        else:
                            data[key] = value
                    
                    char_buffer = ''
                    reading_key = True
                else:
                    char_buffer += char
        
        if char_buffer:
            value = char_buffer.strip()
            try:

                parsed_value = ast.literal_eval(value)
                data[key] = parsed_value
            except (ValueError, SyntaxError):
               
                if value.startswith('"') and value.endswith('"'):
                    data[key] = value.strip('"')
                elif value == 'true':
                    data[key] = True
                elif value == 'false':
                    data[key] = False
                elif value == 'null':
                    data[key] = None
                else:
                    data[key] = value
        
        return json.dumps(data, ensure_ascii=False)

   @staticmethod
   def loadJsonValues(json_values, replace = "¸"):

       json_values = base64.b64decode(json_values).decode('utf-8')
       
       json_values = InitJsonLoader.parse_custom_string_correctly(json_values)

       json_values = json.loads(json_values)
       for key, value in json_values.items():
            if isinstance(value, str):
                json_values[key] = value.replace(';u7q7b;', replace).replace(';v7K7bT;', '/')
                
       return json_values