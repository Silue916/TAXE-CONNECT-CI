import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../..')))
from bin.database.config.process.envLoader import EnvLoader
from bin.database.config.getConnexion.etablishConnexion.sqldbConnexion import SqldbConnexion as sqlConnexion
from bin.database.config.getConnexion.etablishConnexion.noSqldbConnexion import NoSqldbConnexion as noSqlConnexion

class CheckDatabase:
    
    @staticmethod
    def dbConnect( db: int):
        
        config = CheckDatabase.env(db)
        main_driver = config.get('DRIVER', '').lower()

        drivers = {
            "sqlite": sqlConnexion.sqLite,
            "mysql": sqlConnexion.mysql,
            "pgsql": sqlConnexion.postgreSQL,
            "sqlserver": sqlConnexion.sqlServer,
            "oracle": sqlConnexion.oracle,
            "mongodb": noSqlConnexion.mongodb,
            "redis": noSqlConnexion.redis,
        }

        if main_driver in drivers:
            return drivers[main_driver](config)
        else:
            raise ValueError(f"Driver '{main_driver}' not supported")
    
    @staticmethod
    def env(db: int):
        
        envVariables = EnvLoader.get_all_db_vars(db)
        return envVariables