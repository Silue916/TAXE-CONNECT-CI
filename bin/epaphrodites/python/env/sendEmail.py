import os
import sys
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.application import MIMEApplication

sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader
from smtpConfig import smtpConfig

class SendEmail:

    @staticmethod
    def send_email(recipient, subject, content, fichiers=None):

        config = smtpConfig.configurer_email()
        
        try:
            msg = MIMEMultipart()
            msg['From'] = config["users"]
            msg['To'] = ", ".join(recipient)
            
            if config.get('no_replay') and config['no_replay'] != config["users"]:
                msg.add_header('Reply-To', config['no_replay'])
            
            msg['Subject'] = subject
            
            msg.attach(MIMEText(content, 'html'))
            
            if fichiers:
                for fichier in fichiers:
                    if os.path.exists(fichier):
                        with open(fichier, 'rb') as f:
                            piece_jointe = MIMEApplication(f.read(), _subtype="pdf")
                            piece_jointe.add_header('Content-Disposition', 'attachment', filename=os.path.basename(fichier))
                            msg.attach(piece_jointe)
            
            with smtplib.SMTP(config["server"], config["port"]) as server:
                server.starttls()
                server.login(config["users"], config["password"])
                server.send_message(msg)
        
        except Exception as e:
            print(f"Error sending email: {e}")
            raise

def main():
    
    if len(sys.argv) < 2:
        print("Usage: python sendEmail.py '<json_values>'")
        sys.exit(1)
    
    json_values_encoded = sys.argv[1]

    json_datas = InitJsonLoader.loadJsonValues(json_values_encoded, ',')
    
    if 'recipient' not in json_datas or 'subject' not in json_datas or 'content' not in json_datas:
        print("The JSON file must contain 'recipient', 'subject' and 'content'.")
        sys.exit(1)
    
    try:
        SendEmail.send_email(
            recipient=json_datas['recipient'],
            subject=json_datas['subject'],
            content=json_datas['content']
        )
        print("E-mail successfully sent!")
    
    except ValueError as e:
        print(str(e))
        sys.exit(1)

if __name__ == "__main__":
    main()