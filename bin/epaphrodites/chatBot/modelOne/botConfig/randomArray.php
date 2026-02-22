<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait randomArray
{    
    /**
     * @param null|array &$array
     * @return string
    */
    private function answersChanging(
        ?array &$array = null
    ): string {
        // Check if $array is null or empty
        if ($array === null || empty($array)) {
            // Return null if $array is null or empty
            return null;
        }

        // Create an array of keys from the input array
        $keys = array_keys($array);

        // Check for keys already selected
        static $selected = [];
        $availableKeys = array_diff($keys, $selected);

        // Check if all elements have been selected
        if (empty($availableKeys)) {
            // Reset selected elements if all have been used
            $selected = [];
            $availableKeys = $keys;
        }

        // Shuffle available keys
        shuffle($availableKeys);

        // Select the first available key
        $randomKey = $availableKeys[0];
        $selected[] = $randomKey;

        // Return the element corresponding to the selected key
        return $array[$randomKey];
    }
}
