import sys
from Crypto.Cipher import AES
from base64 import b64encode, b64decode
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class EncryptDecrypt:

    @staticmethod
    def encryptValue(dataToEncrypt, key):
        data_to_encrypt = dataToEncrypt.encode()
        cipher = AES.new(key, AES.MODE_GCM)
        ciphertext, tag = cipher.encrypt_and_digest(data_to_encrypt)
        nonce = cipher.nonce
        encrypted_data = b64encode(nonce + ciphertext + tag).decode()
        return encrypted_data

    @staticmethod
    def decryptValue(dataToDecrypt, key):
        decoded_data = b64decode(dataToDecrypt.encode())
        nonce = decoded_data[:16]
        ciphertext = decoded_data[16:-16]
        tag = decoded_data[-16:]
        cipher = AES.new(key, AES.MODE_GCM, nonce=nonce)
        decrypted_data = cipher.decrypt_and_verify(ciphertext, tag)
        return decrypted_data.decode()

if __name__ == "__main__":
    # This secret key must be 32 bytes
    SECRET_KEY = b'Epaphrodite_framework_SECRET_KEY'

    if len(sys.argv) != 2:
        print("Usage: python script.py <json_values>")
        sys.exit(1)

    json_values = sys.argv[1]
    actions = InitJsonLoader.loadJsonValues(json_values, ',')

    if 'function' not in actions or 'value' not in actions:
        print("The JSON file must contain 'function' and 'value'.")
        sys.exit(1)

    json_function = actions['function']

    if json_function == "encryptValue" or json_function == "decryptValue":
        result = getattr(EncryptDecrypt, json_function)(actions['value'], SECRET_KEY)
        print(result)
    else:
        print(f"The function '{json_function}' is not recognized.")
        sys.exit(1)
