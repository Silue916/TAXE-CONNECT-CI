import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../..')))
    
from bin.database.config.process.checkDatabase import CheckDatabase

class SwitchDatabase(CheckDatabase):

    def __init__(self):
        super().__init__()    