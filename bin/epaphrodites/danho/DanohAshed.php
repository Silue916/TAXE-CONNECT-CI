<?php

namespace Epaphrodites\epaphrodites\danho;

use Epaphrodites\epaphrodites\define\config\traits\currentSetGuardSecure;

class DanohAshed
{
    use currentSetGuardSecure;

    protected function Hashed( $Password ){

       return password_hash( $Password , static::$Algorithm);        
    }

    protected function HashGost( $Password ){
        
        return hash( static::$Gost , $Password);
        
    }

    protected function EncryptDatas( $DataToEncrypt ) {
        
        $iv = openssl_random_pseudo_bytes(16);

        $encryptedData = openssl_encrypt($DataToEncrypt, static::$Openssl, static::$GuardKeys , 0, $iv);

        return base64_encode($iv . $encryptedData);

    }
    
    protected function DecryptDatas($EncryptedData) {
        
        $EncryptedData = str_pad( $EncryptedData , 56, '0', STR_PAD_RIGHT);     

        $data = base64_decode($EncryptedData);
        
        $iv = substr($data, 0, 16);

        $encryptedDataWithoutIV = substr($data, 16);

        return openssl_decrypt($encryptedDataWithoutIV, static::$Openssl , static::$GuardKeys , 0, $iv);
    }
}