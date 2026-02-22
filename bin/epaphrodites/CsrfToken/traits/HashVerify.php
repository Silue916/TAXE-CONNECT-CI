<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken\traits;

trait HashVerify
{
    /**
     * Verifies if two hash values are identical.
     *
     * @param string $hashedValue The hash to verify.
     * @param string $inputToken The input hash to compare.
     * @param string $secureToken The secure hash to compare.
     * @return bool True if the hashes are identical, otherwise false.
     */
    public static function verifyHashes(
        ?string $hashedValue = '', 
        ?string $inputToken = '', 
        ?string $secureToken = ''
    ): bool{

        return hash_equals($hashedValue, $inputToken) && hash_equals($hashedValue, $secureToken) && hash_equals($inputToken, $secureToken);
    }

    /**
     * Verifies if two input hashes are identical.
     *
     * @param string $hashedInput The first input hash to verify.
     * @param string $hashedValue The second input hash to compare.
     * @return bool True if the input hashes are identical, otherwise false.
     */
    public static function verifyInputHashes(?string $hashedInput = '', ?string $hashedValue = '' ): bool
    {
        return hash_equals($hashedInput, $hashedValue);
    }

    /**
     * Generic function to generate a hash using the GOST algorithm.
     *
     * @param string|int|null $data The data to hash.
     * @return string The generated hash or an empty string if data is invalid.
     */
    public function gostHash(string|int|null $data = null): string
    {
        return !empty($data) && $data !== 0 
            ? hash('gost', (string)$data) 
            : '';
    }

}

