
import os
import configparser

CONFIG_PATH = os.path.join('bin/config', 'email.ini')

class smtpConfig:
    
    @staticmethod
    def configurer_email():

        if not os.path.exists(CONFIG_PATH):
            raise FileNotFoundError(f"The {CONFIG_PATH} configuration file does not exist.")
        
        config = configparser.ConfigParser()
        config.read(CONFIG_PATH)
        
        return {
            "server": config['EMAIL']['SERVER'],
            "port": config.getint('EMAIL', 'PORT'),
            "users": config['EMAIL']['USER'],
            "password": config['EMAIL']['PASSWORD'],
            "no_replay": config['EMAIL'].get('HIDE_EMAIL', config['EMAIL']['USER'])
        }