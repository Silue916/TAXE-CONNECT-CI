import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../..')))
from bin.database.query.buildChaines.builQueryChaines import db

class Select:
    
    def getUsersData():
        
        results = db(1).select("SELECT * FROM usersaccount").get()
        return results