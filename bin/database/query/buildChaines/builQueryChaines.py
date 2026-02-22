import os
import sys
import json
from datetime import datetime, date, time
from decimal import Decimal
import logging

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../..')))
from bin.database.query.buildQuery.buildQuery import BuildQuery

logger = logging.getLogger(__name__)

class JSONEncoder(json.JSONEncoder):

    def default(self, obj):
        if isinstance(obj, (datetime, date)):
            return obj.isoformat()
        elif isinstance(obj, time):
            return obj.isoformat()
        elif isinstance(obj, Decimal):
            return float(obj)
        elif isinstance(obj, bytes):
            try:
                return obj.decode('utf-8')
            except UnicodeDecodeError:
                return obj.hex()
        elif hasattr(obj, '__dict__'):
            return obj.__dict__
        return super().default(obj)

class QueryBuilder:
    def __init__(self, connection):
        self.connection = connection
        self.query = ""
        self.db_type = self._detect_database_type()
        
    def _detect_database_type(self):

        try:
            conn_type = type(self.connection).__name__
            if 'mysql' in conn_type.lower() or hasattr(self.connection, 'get_server_info'):
                return 'mysql'
            elif 'psycopg2' in str(type(self.connection)) or hasattr(self.connection, 'get_dsn_parameters'):
                return 'postgresql'
            elif 'sqlite' in conn_type.lower() or hasattr(self.connection, 'execute'):
                return 'sqlite'
            elif 'oracle' in conn_type.lower():
                return 'oracle'
            elif 'pyodbc' in conn_type.lower():
                return 'sqlserver'
            else:
                return 'unknown'
        except Exception as e:
            logger.warning(f"Could not detect database type: {e}")
            return 'unknown'
    
    def _convert_row_to_dict(self, row, columns):
        
        if isinstance(row, dict):
            return self._serialize_dict_values(row)
        
        row_dict = {}
        for i, value in enumerate(row):
            column_name = columns[i] if i < len(columns) else f"column_{i}"
            row_dict[column_name] = self._serialize_value(value)
        
        return row_dict
    
    def _serialize_value(self, value):
        
        if isinstance(value, (datetime, date)):
            return value.isoformat()
        elif isinstance(value, time):
            return value.isoformat()
        elif isinstance(value, Decimal):
            return float(value)
        elif isinstance(value, bytes):
            try:
                return value.decode('utf-8')
            except UnicodeDecodeError:
                return value.hex()
        elif value is None:
            return None
        else:
            return value
    
    def _serialize_dict_values(self, data_dict):
        
        serialized = {}
        for key, value in data_dict.items():
            if isinstance(value, dict):
                serialized[key] = self._serialize_dict_values(value)
            elif isinstance(value, list):
                serialized[key] = [self._serialize_value(item) for item in value]
            else:
                serialized[key] = self._serialize_value(value)
        return serialized
    
    def _get_column_names(self, cursor):
        
        try:
            if self.db_type == 'mysql':
                return [desc[0] for desc in cursor.description] if cursor.description else []
            elif self.db_type == 'postgresql':
                return [desc.name for desc in cursor.description] if cursor.description else []
            elif self.db_type == 'sqlite':
                return [desc[0] for desc in cursor.description] if cursor.description else []
            elif self.db_type == 'oracle':
                return [desc[0] for desc in cursor.description] if cursor.description else []
            elif self.db_type == 'sqlserver':
                return [desc[0] for desc in cursor.description] if cursor.description else []
            else:
               
                return [desc[0] for desc in cursor.description] if cursor.description else []
        except Exception as e:
            logger.warning(f"Could not get column names: {e}")
            return []

    def select(self, query: str):
        
        self.query = query
        return self

    def insert(self, query: str):
        
        self.query = query
        return self

    def update(self, query: str):
        
        self.query = query
        return self

    def delete(self, query: str):
        
        self.query = query
        return self

    def get(self, as_dict=True):

        cursor = None
        try:
            cursor = self.connection.cursor()
            cursor.execute(self.query)
            rows = cursor.fetchall()
            
            if not rows:
                return []
            
            if as_dict:
                columns = self._get_column_names(cursor)
                result = []
                for row in rows:
                    row_dict = self._convert_row_to_dict(row, columns)
                    result.append(row_dict)
                return result
            else:
    
                serialized_rows = []
                for row in rows:
                    if isinstance(row, (list, tuple)):
                        serialized_row = tuple(self._serialize_value(value) for value in row)
                    else:
                        serialized_row = self._serialize_value(row)
                    serialized_rows.append(serialized_row)
                return serialized_rows
            
        except Exception as e:
            logger.error(f"Error during query execution: {e}")
            logger.error(f"Query: {self.query}")
            return None
        finally:
            if cursor:
                cursor.close()

    def get_one(self, as_dict=True):

        cursor = None
        try:
            cursor = self.connection.cursor()
            cursor.execute(self.query)
            row = cursor.fetchone()
            
            if not row:
                return None
            
            if as_dict:
                columns = self._get_column_names(cursor)
                return self._convert_row_to_dict(row, columns)
            else:
                if isinstance(row, (list, tuple)):
                    return tuple(self._serialize_value(value) for value in row)
                else:
                    return self._serialize_value(row)
            
        except Exception as e:
            logger.error(f"Error during query execution: {e}")
            logger.error(f"Query: {self.query}")
            return None
        finally:
            if cursor:
                cursor.close()

    def execute(self):
        
        cursor = None
        try:
            cursor = self.connection.cursor()
            cursor.execute(self.query)
            
            if hasattr(self.connection, 'commit'):
                self.connection.commit()
            
            if hasattr(cursor, 'rowcount'):
                affected_rows = cursor.rowcount
                logger.info(f"Query executed successfully. Rows affected: {affected_rows}")
                return affected_rows
            
            return True
            
        except Exception as e:
            logger.error(f"Error during query execution: {e}")
            logger.error(f"Query: {self.query}")
            
            if hasattr(self.connection, 'rollback'):
                try:
                    self.connection.rollback()
                except Exception as rollback_error:
                    logger.error(f"Error during rollback: {rollback_error}")
            
            return False
        finally:
            if cursor:
                cursor.close()

    def to_json(self, results=None):

        if results is None:
            results = self.get()
        
        if results is None:
            return json.dumps({"error": "No results or query failed"})
        
        try:
            return json.dumps(results, cls=JSONEncoder, ensure_ascii=False, indent=2)
        except Exception as e:
            logger.error(f"Error converting to JSON: {e}")
            return json.dumps({"error": f"JSON conversion failed: {str(e)}"})

def db(n: int):
    try:
        conn = BuildQuery.sdb(n)
        return QueryBuilder(conn)
    except Exception as e:
        logger.error(f"Error creating database connection: {e}")
        return None