import sys
import os
import logging
from typing import Dict, Any, Union, Optional

logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

try:
    import pymongo
    from pymongo import MongoClient
    from pymongo.errors import ConnectionFailure, ServerSelectionTimeoutError, ConfigurationError as MongoConfigError
    PYMONGO_AVAILABLE = True
except ImportError as e:
    logger.warning(f"MongoDB driver not available: {e}")
    PYMONGO_AVAILABLE = False

try:
    import redis
    from redis.exceptions import ConnectionError as RedisConnectionError, TimeoutError as RedisTimeoutError
    REDIS_AVAILABLE = True
except ImportError as e:
    logger.warning(f"Redis driver not available: {e}")
    REDIS_AVAILABLE = False

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../../..')))

class DatabaseConnectionError(Exception):
    
    def __init__(self, db_type: str, message: str, original_error: Exception = None):
        self.db_type = db_type
        self.original_error = original_error
        super().__init__(message)

class ConfigurationError(Exception):
    pass

class NoSqldbConnexion:
    
    REQUIRED_FIELDS = {
        'mongodb': ['HOST', 'PORT', 'DATABASE'],
        'redis': ['HOST', 'PORT']
    }
    
    DEFAULT_PORTS = {
        'mongodb': 27017,
        'redis': 6379
    }
    
    @staticmethod
    def _validate_config(config: Dict[str, Any], db_type: str) -> None:

        if not isinstance(config, dict):
            raise ConfigurationError(f"Configuration must be a dictionary for {db_type}")
        
        required_fields = NoSqldbConnexion.REQUIRED_FIELDS.get(db_type, [])
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
        
        if not sanitized.get('PORT') and db_type in NoSqldbConnexion.DEFAULT_PORTS:
            sanitized['PORT'] = NoSqldbConnexion.DEFAULT_PORTS[db_type]
        
        return sanitized
    
    @staticmethod
    def _check_driver_availability(db_type: str) -> None:
        
        availability = {
            'mongodb': PYMONGO_AVAILABLE,
            'redis': REDIS_AVAILABLE
        }
        
        if not availability.get(db_type, False):
            raise DatabaseConnectionError(
                db_type, 
                f"Driver not available For {db_type}. Install the corresponding library."
            )
    
    @staticmethod
    def mongodb(config: Dict[str, Any]) -> Union[object, str]:
        
        db_type = 'mongodb'
        
        try:
            NoSqldbConnexion._check_driver_availability(db_type)
            NoSqldbConnexion._validate_config(config, db_type)
            clean_config = NoSqldbConnexion._sanitize_config(config, db_type)
            
            auth_part = ""
            if clean_config.get('USER') and clean_config.get('PASSWORD'):
                auth_part = f"{clean_config['USER']}:{clean_config['PASSWORD']}@"
            
            uri = f"mongodb://{auth_part}{clean_config['HOST']}:{clean_config['PORT']}/{clean_config['DATABASE']}"
            
            client_options = {
                'serverSelectionTimeoutMS': 30000,
                'connectTimeoutMS': 30000,
                'socketTimeoutMS': 30000,
                'maxPoolSize': 50,
                'minPoolSize': 5,
                'maxIdleTimeMS': 300000,
                'retryWrites': True,
                'retryReads': True,
                'readPreference': 'primary',
                'w': 1,
                'j': True,
                'appName': 'NoSqldbConnexion'
            }
            
            client = MongoClient(uri, **client_options)
            
            database = client[clean_config['DATABASE']]
            
            return database
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except (ConnectionFailure, ServerSelectionTimeoutError) as e:
            error_msg = f"Connection error MongoDB : {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except MongoConfigError as e:
            error_msg = f"Erreur de configuration MongoDB : {e}"
            logger.error(error_msg)
            raise ConfigurationError(error_msg)
        except Exception as e:
            error_msg = f"Unexpected error MongoDB : {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
    
    @staticmethod
    def redis(config: Dict[str, Any]) -> Union[object, str]:

        db_type = 'redis'
        
        try:
            NoSqldbConnexion._check_driver_availability(db_type)
            NoSqldbConnexion._validate_config(config, db_type)
            clean_config = NoSqldbConnexion._sanitize_config(config, db_type)
            
            redis_options = {
                'host': clean_config['HOST'],
                'port': int(clean_config['PORT']),
                'socket_timeout': 30,
                'socket_connect_timeout': 30,
                'socket_keepalive': True,
                'socket_keepalive_options': {},
                'retry_on_timeout': True,
                'retry_on_error': [ConnectionError, TimeoutError],
                'health_check_interval': 30,
                'max_connections': 50,
                'decode_responses': True,
                'encoding': 'utf-8'
            }
            
            if clean_config.get('PASSWORD'):
                redis_options['password'] = clean_config['PASSWORD']
            
            if clean_config.get('DATABASE'):
                try:
                    redis_options['db'] = int(clean_config['DATABASE'])
                except (ValueError, TypeError):
                    logger.warning(f"Invalid Redis database: {clean_config['DATABASE']}, using the DB 0")
                    redis_options['db'] = 0
            else:
                redis_options['db'] = 0
            
            connection_pool = redis.ConnectionPool(**redis_options)
            client = redis.Redis(connection_pool=connection_pool)
            
            return client
            
        except (ConfigurationError, DatabaseConnectionError):
            raise
        except (RedisConnectionError, RedisTimeoutError) as e:
            error_msg = f"Connection error Redis : {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)
        except Exception as e:
            error_msg = f"Unexpected error Redis : {e}"
            logger.error(error_msg)
            raise DatabaseConnectionError(db_type, error_msg, e)