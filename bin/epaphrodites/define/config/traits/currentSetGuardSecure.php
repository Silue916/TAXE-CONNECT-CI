<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

trait currentSetGuardSecure
{

    /**
     * First Hash method (using PASSWORD_DEFAULT constant)
     */
    protected static $Algorithm = PASSWORD_DEFAULT;

    /**
     * Second Hash method (using GOST algorithm)
     */
    protected static $Gost = 'gost';

    /**
     * Encryption method using OpenSSL (AES-256-CBC)
     */
    protected static $Openssl = 'AES-256-CBC';

    /**
     * Key used for encryption and decryption (Base64 encoded)
     */
    protected static $GuardKeys = '5v5Hd+vXmx8fkm4VOr8mm0NiQnU2d1Q2SGdMRW9yUzZXRytGcWc9PQ==';

    /**
     * Characters used for generating security-related strings
     */
    protected static $Guardlatters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

}
