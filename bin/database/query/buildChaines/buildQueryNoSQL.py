import os
import sys
import json
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../..')))
from bin.database.query.buildQuery.buildQuery import BuildQuery

class MongoQueryBuilder:
    def __init__(self, connection):
        self.connection = connection
        self.collection = None

    def collection_name(self, name):
        self.collection = self.connection[name]
        return self

    def find(self, query=None):
        query = query or {}
        return list(self.collection.find(query))

    def insert(self, document):
        return self.collection.insert_one(document).inserted_id

    def update(self, filter_query, new_values):
        return self.collection.update_many(filter_query, {"$set": new_values})

    def delete(self, query):
        return self.collection.delete_many(query)

    def count(self, query=None):
        query = query or {}
        return self.collection.count_documents(query)


class RedisQueryBuilder:
    def __init__(self, connection):
        self.connection = connection

    def set(self, key, value, ex=None):
        if isinstance(value, (dict, list)):
            value = json.dumps(value)
        return self.connection.set(key, value, ex=ex)

    def get(self, key):
        value = self.connection.get(key)
        try:
            return json.loads(value)
        except (TypeError, json.JSONDecodeError):
            return value

    def delete(self, key):
        return self.connection.delete(key)

    def exists(self, key):
        return self.connection.exists(key)

    def incr(self, key, amount=1):
        return self.connection.incr(key, amount)

def mongo(n: int):

    connection = BuildQuery.ndb(n)
    return MongoQueryBuilder(connection)

def redis(n: int):
    
    connection = BuildQuery.ndb(n)
    return RedisQueryBuilder(connection)
