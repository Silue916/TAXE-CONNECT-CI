<?php

namespace Epaphrodites\epaphrodites\env;

class MatchCharactere {
    /**
     * Check if a string doesn't contain any numbers.
     *
     * @param mixed $inputCharacters
     * @return bool
     */
    protected function withoutNumber($inputCharacters): bool {
        return preg_match("/[0-9]/", $inputCharacters) === 0;
    }

    /**
     * Check if a string doesn't contain any characters.
     *
     * @param mixed $inputCharacters
     * @return bool
     */
    protected function withoutCharacters($inputCharacters): bool {
        return preg_match("/[a-zA-Z]/", $inputCharacters) === 0;
    }

    /**
     * Check if a string doesn't contain any characters or numbers.
     *
     * @param mixed $inputCharacters
     * @return bool
     */
    protected function withoutNumberAndCharacters($inputCharacters): bool {
        return preg_match("/[a-zA-Z0-9]/", $inputCharacters) === 0;
    }

    /**
     * Count the number of characters in a string.
     *
     * @param mixed $inputCharacters
     * @return int
     */
    protected function countCharacterNumber($inputCharacters): int {
        return strlen($inputCharacters);
    }
}
