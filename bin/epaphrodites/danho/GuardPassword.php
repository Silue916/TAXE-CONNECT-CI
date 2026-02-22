<?php

namespace Epaphrodites\epaphrodites\danho;

class GuardPassword extends DanohAshed
{

    /**
     * @param mixed $InsidePassword
     * @param mixed $FromInput
     * @return bool
     */
    public function AuthenticatedPassword($InsidePassword , $FromInput):bool
    {
        
        return password_verify($FromInput , $InsidePassword) ? true : false;
    }

    /**
     * @param mixed $charatere
     * @return string
     */
    public function CryptPassword($charatere):string{

        return $this->Hashed($charatere);
    }

    /**
     * @param mixed $charatere
     * @return mixed
     */    
    public function GostCrypt($charatere):mixed{

        return $this->HashGost($charatere);
    }

    /**
     * @param mixed $charatere
     * @return mixed
     */    
    public function OpenSslEncrypt($charatere):mixed{

        return $this->EncryptDatas($charatere);
    } 
    
    /**
     * @param mixed $charatere
     * @return mixed
     */    
    public function OpenSslDecrypt($charatere):mixed{

        return $this->DecryptDatas($charatere);
    }     
}