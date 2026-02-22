<?php

namespace Epaphrodites\epaphrodites\env\phpEnv;

use DateTime;
use IntlDateFormatter;

trait phpEnv{

    private string $chaineTranslate;

    /**
     * Truncate a string to a specified length with optional separator and tail.
     *
     * @param string|null $string The input string to be truncated.
     * @param int $limit The maximum length of the truncated string.
     * @param string $separator The separator to add after the truncated content.
     * @param string $tail The tail to append after the separator.
     * @return string The truncated and formatted string.
     */
    public function truncate(
        string|null $string = null, 
        int $limit = 100, 
        string $separator = '...', 
        string $tail = ''
    ):string
    {
        if (strlen($string) > $limit) {
            // Truncate the string to the specified limit
            $string = rtrim(mb_strimwidth($string, 0, $limit, '', 'UTF-8')) . $separator . $tail;
        }

        // Return the truncated and formatted string
        return $this->chaine($string);
    }

    /**
     * Converts the keys of a multidimensional array to lowercase.
     *
     * @param array $datas The input multidimensional array.
     * @return array The multidimensional array with keys in lowercase.
     */
    public function dictKeyToLowers(
        array $datas = []
    ): array{
    $resultDatas = [];

        if(!empty($datas)){

            foreach ($datas as $outerArray) {
                // If the current element is not an array, skip it
                if (!is_array($outerArray)) {
                    continue;
                }
    
                // Convert the keys of the current array to lowercase and add it to the result
                $resultDatas[] = array_change_key_case($outerArray, CASE_LOWER);
            }
        }

        return $resultDatas;
    }

    /** 
     * @param mixed $date
     **/
    public function date_chaine(
        mixed $date
    ):bool|string{
        $formatter = new IntlDateFormatter('fr_FR.utf8', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

        $timestamp = strtotime($date);

        return $formatter->format($timestamp);
    }

    /**
     * @param mixed $date
     * @return void
     */
    public function LongDate($date)
    {

        $dateTime = new DateTime($date);

        $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::MEDIUM);

        $dateLong = $formatter->format($dateTime);

        echo $dateLong;
    }

    /**
     * @param string $dateString
     * @param string $format
     * @return string
    */
    function convertDateToPHP(
        string $dateString, 
        string $format
    ):string{
        $formats = match (_FIRST_DRIVER_) {
            'oracle' => ['d-M-y h.i.s.u A', 'd-M-y', 'Y-m-d H:i:s', 'Y-m-d'],
            'sqlserver' => ['Y-m-d H:i:s.u', 'Y-m-d H:i:s', 'Y-m-d'],
            default => ['Y-m-d H:i:s', 'Y-m-d', DATE_ATOM],
        };

        foreach ($formats as $sourceFormat) {
            $date = DateTime::createFromFormat($sourceFormat, $dateString);
            if ($date instanceof DateTime) {
                return $date->format($format);
            }
        }

        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date($format, $timestamp);
        }

        return $dateString;
    }    

    /**
     * Transform to ISO code
     * @param string|null $chaine
     * 
     * @return mixed
     */
    public function chaine(
        string|null $chaine = null
    )
    {
        if (empty($chaine)) {
            return null;
        }

        return match (true) {
            (bool)preg_match('/&#039;/', $chaine) => str_replace('&#039;', "'", $chaine),
            (bool)preg_match('/&#224;/', $chaine) => str_replace('&#224;', 'à', $chaine),
            (bool)preg_match('/&#225;/', $chaine) => str_replace('&#225;', 'á', $chaine),
            (bool)preg_match('/&#226;/', $chaine) => str_replace('&#226;', 'â', $chaine),
            (bool)preg_match('/&#227;/', $chaine) => str_replace('&#227;', 'ã', $chaine),
            (bool)preg_match('/&#228;/', $chaine) => str_replace('&#228;', 'ä', $chaine),
            (bool)preg_match('/&#230;/', $chaine) => str_replace('&#230;', 'æ', $chaine),
            (bool)preg_match('/&#231;/', $chaine) => str_replace('&#231;', 'ç', $chaine),
            (bool)preg_match('/&#232;/', $chaine) => str_replace('&#232;', 'è', $chaine),
            (bool)preg_match('/&#233;/', $chaine) => str_replace('&#233;', 'é', $chaine),
            (bool)preg_match('/&#234;/', $chaine) => str_replace('&#234;', 'ê', $chaine),
            (bool)preg_match('/&#235;/', $chaine) => str_replace('&#235;', 'ë', $chaine),
            (bool)preg_match('/&#238;/', $chaine) => str_replace('&#238;', 'î', $chaine),
            (bool)preg_match('/&#239;/', $chaine) => str_replace('&#239;', 'ï', $chaine),
            (bool)preg_match('/&#244;/', $chaine) => str_replace('&#244;', 'ô', $chaine),
            (bool)preg_match('/&#251;/', $chaine) => str_replace('&#251;', 'û', $chaine),
            (bool)preg_match('/&amp;/', $chaine) => str_replace('&amp;', '&', $chaine),
            default => $chaine,
        };
    }

    /**
     * For transcoding values in an Excel generated (french)
     *
     * @param string $chaine
     * @return string
     */
    public function translate_fr(
        string $chaine
    ):string{

        $this->chaineTranslate = iconv('Windows-1252', 'UTF-8//TRANSLIT', $chaine);

        return $this->chaineTranslate;
    }

    /**
     * @param string $chaine
     * @return bool
    */
    public function startsWithLetter(
        string $chaine
    ):bool {
       
        return preg_match('/^[a-zA-Z]/', $chaine) === 1;
    }

    /**
     * Uploads files based on the provided array of paths and file keys.
     *
     * @param array $pathsAndFiles Associative array mapping destination paths to $_FILES keys.
     * @param bool $useNumericKey Whether to use a numeric key to access uploaded files.
     * @return bool Returns true if all files are successfully uploaded, false otherwise.
     */
    public function uploadFiles(
        array $pathsAndFiles = [], 
        bool $useNumericKey = false
    ): bool{
        if (empty($pathsAndFiles)) {
            return false;
        }

        $allUploaded = true;
        foreach ($pathsAndFiles as $targetPath => $fileKey) {
            $fileInfo = $_FILES[$fileKey];
            if (!isset($fileInfo) || !is_uploaded_file($useNumericKey ? $fileInfo['tmp_name'][0] : $fileInfo['tmp_name'])) {
                $allUploaded = false;
                continue;
            }

            $fileName = $useNumericKey ? $fileInfo['name'][0] : $fileInfo['name'];
            $safeFileName = $this->generateSafeFileName($fileName);
            if (!$safeFileName) {
                $allUploaded = false;
                continue;
            }

            $fullTargetPath = rtrim($targetPath, '/') . '/' . $safeFileName;
            $tmpName = $useNumericKey ? $fileInfo['tmp_name'][0] : $fileInfo['tmp_name'];
            if (!move_uploaded_file($tmpName, $fullTargetPath)) {
                $allUploaded = false;
            }
        }

        return $allUploaded;
    }

    /**
     * @param string $intFileName
     * @return string|false
     */
    protected function generateSafeFileName(
        string $intFileName
    ): string|false{
        $extension = pathinfo($intFileName, PATHINFO_EXTENSION);
        $baseName = preg_replace('/[^\w\.]/', '_', pathinfo($intFileName, PATHINFO_FILENAME));
        $safeFileName = $baseName . ($extension ? '.' . $extension : '');
        return $safeFileName ?: false;
    }  

    /**
     * Clean directory
     * @param string $directory
     * @param string $Extension
     * @return bool
     */
    public function deleteDirFiles(
        string $directory, 
        string $extension
    ): bool
    {
        if (is_dir($directory)) {
           
            $normalizedDirectory = rtrim($directory, '/') . '/';
            
            $safeExtension = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $extension);
    
            $pattern = $normalizedDirectory . '*' . $safeExtension;
    
            $files = glob($pattern);
    
            if ($files === false) {
                return false;
            }
    
            foreach ($files as $file) {

                if (is_file($file) && !unlink($file)) {
                    return false;
                }
            }
    
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete specific file
     *
     * @param string $Directory
     * @param string $FileName
     * @return bool
     */
    public function deleteFiles(
        string $directory, 
        string $fileName
    ): bool
    {
        $normalizedDirectory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        $filePath = $normalizedDirectory . $fileName;
    
        if (is_file($filePath)) {

            if (unlink($filePath)) {
                return true;
            } else {

                return false;
            }
        } else {

            return false;
        }
    }

    /**
     * Cleans up spaces in a string by trimming leading and trailing spaces,
     * and normalizing internal spaces by replacing multiple spaces with a single space.
     * @param string $datas The input string to be cleaned.
     * @return string The cleaned string.
     */
    public function no_space(
        string $data
    ): string{
        $string = is_numeric($data) ? (string) $data : $data;

        $string = trim($string);
        
        $string = preg_replace('/\s+/', ' ', $string);
    
        return $string;
    }

    /**
     * Formar
     */
    public function nbre_format($num, $dec, $separator)
    {

        return $num !== null ? number_format($num, $dec, ',', ' ') : 0;
    }

    /**
     *
     * @param string|null $inputString
     * @return string
     */
    public function reel(
        string|null $inputString = null
    ):string{

        return str_replace([' ', ','], ['', '.'], $inputString);
    }


    /**
     * Export data in json type
     * @param array|null $datas
     * @return mixed
     */
    public function e_json(
        array $datas = []
    ):bool|string{
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        return json_encode($datas, JSON_PRETTY_PRINT);
    }

    /**
     * Validate an email address securely.
     *
     * @param string $email The email address to validate.
     * @return bool Returns true if the email address is valid, otherwise false.
     */
    public function validateEmail(
        string $email
    ): bool {

        if (empty($email) || strlen($email) > 254) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        [$localPart, $domainPart] = explode('@', $email, 2);

        if (!checkdnsrr($domainPart, 'MX') || filter_var($domainPart, FILTER_VALIDATE_IP)) {
            return false;
        }

        $mxRecords = [];
        if (!getmxrr($domainPart, $mxRecords)) {
            return false;
        }

        if (preg_match('/[\x00-\x1F\x7F-\xFF]/', $localPart)) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param string $chaines|null
     * @return string
     */
    public function explodeDatas(
        string|null $datas = null, 
        string $separator = '', 
        int $nbre = 0
    ):string{

        $chaines = explode($separator, $datas);

        return $chaines[$nbre];
    }

    /**
     * Date format
     */
    public function DateFormat(
        string $stringDate
    ):string|null{

        // Check if the input date string is empty
        if (empty($stringDate)) {
            return null;
        }

        // Create a DateTime object from the input date string
        $dateTime = date_create($stringDate);

        // Return the formatted date in 'Y-m-d' format
        return $dateTime !== false ? date_format($dateTime, 'Y-m-d') : null;
    }

    /**
     * @return string
     */
    public function strpad($number, $pad_length, $pad_string)
    {
        return str_pad($number, $pad_length, $pad_string, STR_PAD_LEFT);
    }
}
