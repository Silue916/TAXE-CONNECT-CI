import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../..')))
from bin.database.config.switchDatabase import SwitchDatabase

class BuildQuery:
    
    @staticmethod
    def sdb( db : int):
        return SwitchDatabase.dbConnect(db)
    
    @staticmethod
    def ndb( db : int):
        
        return SwitchDatabase.dbConnect(db)