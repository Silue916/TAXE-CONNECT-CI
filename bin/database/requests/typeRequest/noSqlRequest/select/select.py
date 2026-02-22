import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../..')))
from bin.database.query.buildChaines.buildQueryNoSQL import mongo, redis

class Select:
    
    def getUsersData():
        result = mongo(6).collection_name("usersaccount").find({})
        return result