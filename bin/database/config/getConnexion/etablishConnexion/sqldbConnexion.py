import sys
import os
import logging
from typing import Dict, Any, Union, Optional

logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

try:
    import psycopg2
    from psycopg2 import OperationalError
    PSYCOPG2_AVAILABLE = True
except ImportError as e:
    logger.warning(f"PostgreSQL driver not available: {e}")
    PSYCOPG2_AVAILABLE = False

try:
    import sqlite3
    SQLITE3_AVAILABLE = True
except ImportError as e:
    logger.warning(f"SQLite driver not available: {e}")
    SQLITE3_AVAILABLE = False

try:
    import mysql.connector
    from mysql.connector import Error as MySQLError
    PYMYSQL_AVAILABLE = True
except ImportError as e:
    logger.warning(f"MySQL driver not available: {e}")
    PYMYSQL_AVAILABLE = False

try:
    import cx_Oracle
    ORACLE_AVAILABLE = True
except ImportError as e:
    logger.warning(f"Oracle driver not available: {e}")
    ORACLE_AVAILABLE = False

try:
    import pyodbc
    PYODBC_AVAILABLE = True
except ImportError as e:
    logger.warning(f"SQL Server driver not available: {e}")
    PYODBC_AVAILABLE = False

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../../..')))

SQLITE_PATH = os.getenv("SQLITE_PATH", "bin/database/datas/SqlLite/")

class DatabaseConnectionError(Exception):

    def __init__(self, db_type: str, message: str, original_error: Exception = None):
        self.db_type = db_type
        self.original_error = original_error
        super().__init__(message)

class ConfigurationError(Exception):

    pass

class SqldbConnexion:
    
    REQUIRED_FIELDS = {
        'pgsql': ['HOST', 'PORT', 'DATABASE', 'USER', 'PASSWORD'],
        'mysql': ['HOST', 'PORT', 'DATABASE', 'USER', 'PASSWORD'],
        'sqlite': ['DATABASE'],
        'oracle': ['HOST', 'PORT', 'DATABASE', 'USER', 'PASSWORD'],
        'sqlserver': ['HOST', 'PORT', 'DATABASE', 'USER', 'PASSWORD']
    }
    
    DEFAULT_PORTS = {
        'pgsql': 5432,
        'mysql': 3306,
        'oracle': 1521,
        'sqlserver': 1433
    }
    
    @staticmethod
    def _validate_config(config: Dict[str, Any], db_type: str) -> None:

        if not isinstance(config, dict):
            raise ConfigurationError(f"Configuration must be a dictionary for {db_type}")
        
        required_fields = SqldbConnexion.REQUIRED_FIELDS.get(db_type, [])
        missing_fields = [field for field in required_fields if not config.get(field)]
        
        if missing_fields:
            raise ConfigurationError(f"Missing fields for {db_type}: {missing_fields}")
        
        if 'PORT' in config and config['PORT']:
            try:
                port = int(config['PORT'])
                if not (1 <= port <= 65535):
                    raise ConfigurationError(f"Invalid port for {db_type}: {port}")
            except (ValueError, TypeError):
                raise ConfigurationError(f"Port must be a number for {db_type}: {config['PORT']}")
    
    @staticmethod
    def _sanitize_config(config: Dict[str, Any], db_type: str) -> Dict[str, Any]:

        sanitized = config.copy()
        
        for key, value in sanitized.items():
            if isinstance(value, str):
                sanitized[key] = value.strip()
        
        if not sanitized.get('PORT') and db_type in SqldbConnexion.DEFAULT_PORTS:
            sanitized['PORT'] = SqldbConnexion.DEFAULT_PORTS[db_type]
            logger.info(f"Default port used for {db_type}: {sanitized['PORT']}")
        
        return sanitized
    
    @staticmethod
    def _check_driver_availability(db_type: str) -> None:
        
        availability = {
            'pgsql': PSYCOPG2_AVAILABLE,
            'mysql': PYMYSQL_AVAILABLE,
            'sqlite': SQLITE3_AVAILABLE,
            'oracle': ORACLE_AVAILABLE,
            'sqlserver': PYODBC_AVAILABLE
        }
        
        if not availability.get(db_type, False):
            raise DatabaseConnectionError(
                db_type, 
                f"Driver not available for {db_type}. Install the corresponding library."
            )
    
    @staticmethod
    def postgreSQL(config: Dict[str, Any]) -> object:

        db_type = 'pgsql'
        
        try:
            SqldbConnexion._check_driver_availability(db_type)
            SqldbConnexion._validate_config(config, db_type)
            clean_config = SqldbConnexion._sanitize_config(config, db_type)
            
            conn = psycopg2.connect(
                dbname=clean_config["DATABASE"],
                user=clean_config["USER"],
                password=clean_config["PASSWORD"],
                host=clean_config["HOST"],
                port=clean_config["PORT"],
                connect_timeout=30,
                application_name="SqldbConnexion"
            )
            
            logger.info(f"PostgreSQL connection established to {clean_config['HOST']}:{clean_config['PORT']}")
            return conn
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except OperationalError as e:
            error_msg = f"Connection error PostgreSQL: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error PostgreSQL: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)

    @staticmethod
    def mysql(config: Dict[str, Any]) -> object:

        db_type = 'mysql'
    
        try:
            SqldbConnexion._check_driver_availability(db_type)
            SqldbConnexion._validate_config(config, db_type)
            clean_config = SqldbConnexion._sanitize_config(config, db_type)
    
            conn = mysql.connector.connect(
                host=clean_config["HOST"],
                user=clean_config["USER"],
                password=clean_config["PASSWORD"],
                database=clean_config["DATABASE"],
                port=clean_config["PORT"],
                connection_timeout=30,
                autocommit=True,
                charset='utf8mb4',
                use_unicode=True
            )
            
            return conn

        except (ConfigurationError, DatabaseConnectionError):
            raise
        except MySQLError as e:
            error_msg = f"Connection error MySQL: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error MySQL: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)

    @staticmethod
    def sqLite(config: Dict[str, Any]) -> object:

        db_type = 'sqlite'
        
        try:
            SqldbConnexion._check_driver_availability(db_type)
            SqldbConnexion._validate_config(config, db_type)
            clean_config = SqldbConnexion._sanitize_config(config, db_type)
            
            db_filename = clean_config["DATABASE"]
            if not db_filename.endswith('.db') and not db_filename.endswith('.sqlite'):
                db_filename += '.db'
            
            db_path = os.path.join(SQLITE_PATH, db_filename)
            
            normalized_path = os.path.normpath(db_path)
            normalized_sqlite_path = os.path.normpath(SQLITE_PATH)
            
            if '..' in db_path or not normalized_path.startswith(normalized_sqlite_path):
                raise ConfigurationError(f"Unsecured database path: {db_path}")
            
            os.makedirs(os.path.dirname(db_path), exist_ok=True)
            
            conn = sqlite3.connect(
                db_path,
                timeout=30,
                check_same_thread=False
            )
            
            conn.execute("PRAGMA journal_mode=WAL")
            conn.execute("PRAGMA synchronous=NORMAL")
            conn.execute("PRAGMA temp_store=MEMORY")
            conn.execute("PRAGMA mmap_size=268435456")
            
            logger.info(f"SQLite connection established to {db_path}")
            return conn
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except sqlite3.Error as e:
            error_msg = f"Connection error SQLite: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error SQLite: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)

    @staticmethod
    def oracle(config: Dict[str, Any]) -> object:

        db_type = 'oracle'
        
        try:
            SqldbConnexion._check_driver_availability(db_type)
            SqldbConnexion._validate_config(config, db_type)
            clean_config = SqldbConnexion._sanitize_config(config, db_type)

            dsn = cx_Oracle.makedsn(
                clean_config["HOST"], 
                clean_config["PORT"], 
                service_name=clean_config["DATABASE"]
            )
            
            conn = cx_Oracle.connect(
                user=clean_config["USER"],
                password=clean_config["PASSWORD"],
                dsn=dsn,
                encoding="UTF-8"
            )
            
            logger.info(f"Oracle connection established to {clean_config['HOST']}:{clean_config['PORT']}")
            return conn
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except cx_Oracle.Error as e:
            error_msg = f"Connection error Oracle: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error Oracle: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)

    @staticmethod
    def sqlServer(config: Dict[str, Any]) -> object:

        db_type = 'sqlserver'
        
        try:
            SqldbConnexion._check_driver_availability(db_type)
            SqldbConnexion._validate_config(config, db_type)
            clean_config = SqldbConnexion._sanitize_config(config, db_type)
            
            conn_str = (
                f"DRIVER={{ODBC Driver 17 for SQL Server}};"
                f"SERVER={clean_config['HOST']},{clean_config['PORT']};"
                f"DATABASE={clean_config['DATABASE']};"
                f"UID={clean_config['USER']};"
                f"PWD={clean_config['PASSWORD']};"
                f"Encrypt=yes;"
                f"TrustServerCertificate=no;"
                f"Connection Timeout=30;"
            )
            
            conn = pyodbc.connect(conn_str)
            
            logger.info(f"SQL Server connection established to {clean_config['HOST']}:{clean_config['PORT']}")
            return conn
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except pyodbc.Error as e:
            error_msg = f"Connection error SQL Server: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error SQL Server: {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)