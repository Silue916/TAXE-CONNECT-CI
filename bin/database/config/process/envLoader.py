import os
import sys
from pathlib import Path
from dotenv import load_dotenv
from typing import Optional

BASE_DIR = Path(__file__).resolve().parents[4]
ENV_PATH = BASE_DIR / "bin" / "config" / ".env"

class EnvLoader:
    _loaded = False
    
    @classmethod
    def _ensure_loaded(cls) -> None:

        if not cls._loaded:
            if ENV_PATH.exists():
                load_dotenv(ENV_PATH)
                cls._loaded = True
            else:
                raise FileNotFoundError(f"No .env file found: {ENV_PATH}")
    
    @classmethod
    def get_env_variable(cls, name: str, db: int) -> Optional[str]:

        cls._ensure_loaded()
        env_name = f"{db}DB_{name}"
        return os.getenv(env_name)
    
    @classmethod
    def get_env_variable_required(cls, name: str, db: int) -> str:

        value = cls.get_env_variable(name, db)
        if value is None:
            env_name = f"{db}DB_{name}"
            raise ValueError(f"Require environnement variable : {env_name}")
        return value
    
    @classmethod
    def get_all_db_vars(cls, db: int) -> dict:

        cls._ensure_loaded()
        prefix = f"{db}DB_"
        return {
            key[len(prefix):]: value 
            for key, value in os.environ.items() 
            if key.startswith(prefix)
        }