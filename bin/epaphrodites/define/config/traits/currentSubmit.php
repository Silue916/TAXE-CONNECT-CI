<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait currentSubmit
{

    use currentFunctionNamespaces;

    private static array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];

    /**
     * @param mixed $key
     * @param string $method
     * @param array $customMethods
     * @return bool
    */
    public static function isSubmit(
        string $key, 
        string $method = 'POST', 
        array $customMethods = []
    ):bool{
        $defaultMethods = [
            'POST' => $_POST,
            'GET' => $_GET,
            'PUT' => array_key_exists('php://input', $customMethods) ? stream_get_contents($customMethods['php://input']) : null,
            'DELETE' => array_key_exists('php://input', $customMethods) ? stream_get_contents($customMethods['php://input']) : null,
        ];
    
        $methods = array_merge($defaultMethods, $customMethods);
    
        $superglobal = match (strtoupper($method)) {
            'POST', 'GET' => $methods[$method],
            'PUT', 'DELETE' => $methods[$method] ?? null,
            default => null,
        };
    
        if ($superglobal === null) {
            return false;
        }
    
        return array_key_exists($key, is_array($superglobal) ? $superglobal : []);
    }

    /**
     * Check if a variable exists in the $_POST array.
     *
     * @param string $key The key to check.
     * @param string $type if is ('int', 'float', 'bool', 'string')
     * @return bool True if the key exists in $_POST, false otherwise.
     * @throws epaphroditeException if key is empty
     */
     public static function isPost($key, $type = 'string'): bool
     {
         if (empty($key)) {
             throw new epaphroditeException('Invalid key');
         }
     
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             return false;
         }
     
         $value = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
     
         if ($value === null || $value === false) {
             return false;
         }
     
         if ($type !== null) {
             $isValid = match ($type) {
                 'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
                 'float' => filter_var($value, FILTER_VALIDATE_FLOAT) !== false,
                 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false,
                 'string' => is_string($value),
                 default => throw new epaphroditeException('Invalid type specified'),
             };
     
             if (!$isValid) {
                 return false;
             }
         }
     
        return !empty(static::noSpace($value)) ? true : false;
     }
     
    /**
     * @param string $accepted
     * @return bool
     */
    public static function isValidMethod(
        bool $crsf = false, 
        string $accepted = 'POST'
    ): bool{

        $crsf === false ? :static::forcingTokenVerification();
        
        // Retrieve and sanitize the request method
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : null;
        
        // Check if the method is not null and is among the allowed methods
        return ($method !== null && in_array($method, self::$allowedMethods) && $method === $accepted);
    }

    /**
     * @param string $accepted
     * @return bool
     */
    public static function isValidApiMethod(bool $crsf = false, string $accepted = 'POST'): bool
    {

        $crsf === false ? :static::forcingApiTokenVerification();

        // Retrieve and sanitize the request method
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : null;
        
        // Check if the method is not null and is among the allowed methods
        return ($method !== null && in_array($method, self::$allowedMethods) && $method === $accepted);
    }    
     
    /**
     * Get the value from $_POST array for a given key with a default value.
     *
     * @param string $key The key to get.
     * @param bool $clean
     * @return mixed The value for the key in $_POST or an empty string if not set.
     */
    public static function getPost($key, bool $clean = false)
    {

        if (!isset($key) || $key === '') {
            throw new \InvalidArgumentException('Invalid key: Key is required and cannot be empty.');
        }
    
        if (empty($key)) {
            throw new epaphroditeException('Invalid key');
        }

        return $clean ? $_POST[$key] : (static::noSpace($_POST[$key]) ?? '') ;
    }

    /**
     * Get data from ajax using method (GET ou POST)
     * @param mixed $key
     * @throws \InvalidArgumentException
     * @throws \JsonException
     */
    public static function isAjax(string $key): ?string
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Invalid key: Key is required and must be a non-empty string.');
        }
    
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $result = match ($method) {
            'POST' => self::isPostJSON($key),
            'GET'  => self::isGetJSON($key),
            default => null,
        };
    
        return is_string($result) ? $result : null;
    }

    /**
     * @param string $key The key in the $_FILES array to check.
     * @return bool True if the file exists and there's no error; otherwise, false.
     */
    public static function isFileName(string $key, bool $num = false): bool {

        return $num == false 
                    ? isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK
                    : isset($_FILES[$key]) && $_FILES[$key]['error'][0] === UPLOAD_ERR_OK;
    } 
    
    /**
     * @param string $key
     * @return string|'name'
     */
    public static function getFileName(
        string $key,
        string $value = 'name',
        bool $num = false
    ): string|null
    {
        if (static::isFileName($key)) {
            $fileName = $_FILES[$key][$value];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $baseName = preg_replace('/[^\w\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
            return $baseName . ($extension ? '.' . $extension : '');
        } elseif (static::isFileName($key, $num)) {
            $fileName = $_FILES[$key][$value][0];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $baseName = preg_replace('/[^\w\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
            return $baseName . ($extension ? '.' . $extension : '');
        }
    
        return null;
    }
    
    /**
     * Check if a variable exists in the $_GET array.
     *
     * @param string $key The key to check.
     * @param string $type if is ('int', 'float', 'bool', 'string')
     * @return bool True if the key exists in $_POST, false otherwise.
     * @throws epaphroditeException if key is empty
     */
    public static function isGet($key, $type = 'string'): bool
    {
        if (empty($key)) {
            throw new epaphroditeException('Invalid key');
        }
    
        $value = filter_input(INPUT_GET, $key, FILTER_DEFAULT);
    
        if ($value === null || $value === false) {
            return false;
        }
    
        if ($type !== null) {
            $isValid = match ($type) {
                'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
                'float' => filter_var($value, FILTER_VALIDATE_FLOAT) !== false,
                'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false,
                'string' => is_string($value),
                default => throw new epaphroditeException('Invalid type specified'),
            };
    
            if (!$isValid) {
                return false;
            }
        }
    
        return !empty(static::noSpace($value)) ? true : false;
    }    

    /**
     * Get the value from $_GET array for a given key with a default value.
     *
     * @param string $key The key to get.
     * @return mixed The value for the key in $_GET or an empty string if not set.
     */
    public static function getGet($key)
    {

        if (empty($key)) {
            throw new epaphroditeException('Invalid key');
        }

        return static::noSpace($_GET[$key]) ?? '';
    }

    /**
     * Process data from a specified method and key, converting elements to integers if they exist.
     *
     * @param string $method The method to retrieve data from (default is 'POST').
     * @param string $key    The key of the data to be processed.
     *
     * @return array Processed array with integer elements or an empty array.
     */
    public static function isArray(string $key, string $method = 'POST'): array
    {

        if (empty($key)) {
            throw new epaphroditeException('Invalid key');
        }

        // Retrieve data based on the specified method and key
        $data = match (strtoupper($method)) {
            'GET' => $_GET[$key] ?? null,
            'POST' => $_POST[$key] ?? null,
            default => null,
        };

        // Check if data is an array and is not empty
        if (is_array($data) && !empty($data)) {
            return $data; // Return the array if it is valid
        }

        // Return the entire data retrieved or an empty array if it doesn't exist
        return is_array($data) ? $data : [];
    }

    /**
     * Checks if the value associated with a specified key in $_POST or $_GET (based on the method) is equal to a given index.
     *
     * @param string $key     The key to check in $_POST or $_GET.
     * @param string|int|null $index    The index or value to compare against.
     * @param string $method  The method to use, either 'POST' or 'GET' (default is 'POST').
     *
     * @return bool Returns true if the value associated with the key matches the given index, otherwise false.
     */
    public static function isSelected(
        string $key, 
        string|int|null $index, 
        string $method = 'POST'
    ): bool{

        if (empty($key) || empty($index)) {
            throw new epaphroditeException('Invalid key');
        }

        $value = filter_input($method === 'GET' ? INPUT_GET : INPUT_POST, $key, FILTER_DEFAULT);

        return $value != null && $value != false && $value == $index;
    }

    /**
     * Checks if specific keys in a given method's data (GET or POST) are not empty.
     *
     * @param string $method The method to check (either 'GET' or 'POST').
     * @param array $keys An array containing keys to check in the data source.
     *
     * @return bool Returns true if all specified keys exist and are not empty in the data source, otherwise false.
     */
    public static function notEmpty(array $keys = [], string $method = 'POST'): bool
    {
        // Validate the HTTP method
        $method = strtoupper($method);
        if (!in_array($method, self::$allowedMethods, true)) {
            throw new \InvalidArgumentException('Invalid HTTP method provided.');
        }

        // Determine the data source based on the method
        $source = match ($method) {
            'GET' => $_GET,
            'POST' => self::filterInputArray(INPUT_POST),
            'PUT' => self::parseRawInputData(),
            default => [],
        };

        // Check if the data source is empty
        if (empty($source)) {
            return false;
        }

        // Check if each specified key exists in the data source and is not empty
        foreach ($keys as $key) {
            
            if (!array_key_exists($key, $source) || empty($source[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if all specified keys in the array have non-empty values in the request data
     * for the given HTTP method.
     *
     * @param array $array The keys to check in the request data.
     * @param string $method The HTTP method of the request (e.g., 'GET', 'POST').
     * @return bool Returns true if all specified keys have non-empty values, false otherwise.
     * @throws epaphroditeException If the provided method is not supported.
     */
    public function arrayNoEmpty(array $array, string $method = "POST"): bool {
        if (!in_array($method, static::$allowedMethods)) {
            throw new epaphroditeException("Invalid method.");
        }

        $data = $this->filterMethod($array, $method);

        foreach ($array as $key) {

            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }

        return true;
    }

   /**
     * Summary of checkKeys
     * 
     * @param array $keys
     * @param array $arrayFrom
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function checkKeys(
        array $keys, 
        array $arrayFrom
    ): bool{

        $result = true;

        foreach ($arrayFrom as $element) {
            
            if (is_array($element) && self::isAssociativeArray($element)) {
               
                foreach ($keys as $key) {
                    if (!array_key_exists($key, $element)) {
                        $result = false;
                        break;
                    }
                }

                return $result;
            }
            
            elseif (is_array($element) && !self::isAssociativeArray($element)) {

                if (count($element) < count($keys)) {
                    $result = false;
                }

                foreach ($keys as $value) {
                    if (!in_array($value, $element)) {
                        $result = false;
                        break;
                    }
                }

                return $result;

            } else {

                throw new \InvalidArgumentException("All elements of the main array must be arrays.");
            }
        }

        return $result;
    }   
    
    /**
     * Decodes a Base64-encoded signature retrieved via an HTTP method.
     * 
     * @param string $signature The encoded string to decode.
     * @param string $method The HTTP method used to retrieve the data ('POST' or 'GET').
     * @return string|null The decoded data on success, or `null` on failure.
     * @throws \InvalidArgumentException If the HTTP method is invalid.
     */
    public static function decodedData(
        string $signature = '', 
        string $method = 'POST'
    ): ?string {
        // Retrieve data based on the HTTP method
        $signature = match ($method) {
            'POST' => static::getPost($signature),
            'GET' => static::getGet($signature),
            default => throw new \InvalidArgumentException("The HTTP method '$method' is not supported.")
        };

        // Check if the signature is not empty
        if (!empty($signature)) {
            // Split the string into two parts (before and after the comma)
            $parts = explode(',', $signature, 2);
            if (count($parts) === 2) {
                [, $base64Data] = $parts;

                // Validate the Base64 format before decoding
                if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $base64Data)) {
                    $decodedData = base64_decode($base64Data, true); // Use strict mode to avoid silent errors
                    if ($decodedData !== false) {
                        return $decodedData;
                    }
                }
            }
        }

        // Return null on failure
        return null;
    }

    /**
     * Saves data to a uniquely named file within a specified directory.
     * 
     * @param string $directory The directory where the file will be created.
     * @param string $data The data to be written to the file.
     * @return bool Returns `true` on success or `false` on failure.
     * @throws \InvalidArgumentException If the directory does not exist or is not writable.
     */
    public static function putContents(
        string $directory, 
        string $data
    ): bool {
     
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException("The specified directory '$directory' does not exist.");
        }

        if (!is_writable($directory)) {
            throw new \InvalidArgumentException("The specified directory '$directory' is not writable.");
        }

        if (empty($data)) {
            return false;
        }

        $filename = md5('signature_' . uniqid('', true)) . '.png';
        $filePath = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $writeResult = file_put_contents($filePath, $data);

        return $writeResult !== false;
    }

    /**
     * Streaming without cache
     * 
     * @param iterable $stream
     * @param bool $withBuffering
     * @return string
     */
    public static function stream(
        mixed $stream,
        bool $withBuffering = true
    ): string {
        $output = '';
        
        if (is_bool($stream)) {

            if ($stream === true) {
                $withBuffering = true;
            }

            $stream = [];
        }
        
        if (!is_array($stream) && !($stream instanceof \Traversable)) {

            $stream = [];
        }
        
        if ($withBuffering && !headers_sent()) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
        }
        
        if ($withBuffering) {
            while (@ob_end_flush());
            ob_implicit_flush(true);
            set_time_limit(0);
            flush();
        }
        
        foreach ($stream as $chunk) {

            $escapedChunk = str_replace(["\n", "\r"], ['\\n', '\\r'], $chunk);
            $output .= $chunk . "\n";
            
            if ($withBuffering) {
                echo "$escapedChunk\n\n";
                flush();
                usleep(100000);
            }
        }
        
        if ($withBuffering) {
            flush();
        }
        
        return $output;
    }

/**
     * Process GET parameters and return as JSON.
     * @param string|null $specificKey
     * @return null|string|array
     */
    private static function isGetJSON(?string $specificKey = null): string|array|null
    {
        if ($specificKey !== null) {
            return isset($_GET[$specificKey]) ? self::noSpace($_GET[$specificKey]) : null;
        }
    
        if (!empty($_GET)) {
            return self::noSpace($_GET);
        }
    
        return null;
    }
    
    /**
     * Process POST parameters and return as JSON.
     * @param string|null $specificKey
     * @return null|string|array
     */
    private static function isPostJSON(?string $specificKey = null): string|array|null
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                try {
                    $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
                    if ($specificKey !== null) {
                        return isset($data[$specificKey]) ? self::noSpace($data[$specificKey]) : null;
                    }
                    return self::noSpace($data);
                } catch (\JsonException $e) {
                    error_log('JSON decoding error: ' . $e->getMessage());
                    return null;
                }
            }
        }

        if ($specificKey !== null) {
            return isset($_POST[$specificKey]) ? self::noSpace($_POST[$specificKey]) : null;
        }

        if (!empty($_POST)) {
            return self::noSpace($_POST);
        }

        return null;
    }    
  
    /**
     * Filters request data based on the HTTP method.
     *
     * @param array $keys The keys to retrieve from the request data.
     * @param string $method The HTTP method of the request.
     * @return array The filtered data from the request.
     * @throws epaphroditeException If the method is not supported for superglobal access.
     */
    private function filterMethod(
        array $keys, 
        string $method
    ): array {
        $data = []; // Initialize $data to avoid issues if the switch does not match
        switch (strtoupper($method)) {
            case 'GET':
                $data = $_GET;
                break;
            case 'POST':
                $data = $_POST;
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $parsedData);
                $data = $parsedData; // Assign parsed data to $data
                break;
            case 'OPTIONS':
                // No specific processing needed for OPTIONS in this example
                break;
            default:
                throw new epaphroditeException("Unsupported method for superglobal access.");
        }
        return $data; // Return $data at the end of the function
    }

    private static function filterInputArray(int $type): array {
        $filtered = filter_input_array($type, [
            '*' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => '']
            ]
        ]);
    
        if ($filtered === null || $filtered === false) {
            throw new \RuntimeException('Failed to retrieve input data.');
        }
    
        return $filtered;
    }
    
    private static function parseRawInputData(): array {
        $rawData = file_get_contents('php://input');
        parse_str($rawData, $putData);
        return array_map('trim', $putData);
    }

    /**
     * Cleans up spaces in a string by trimming leading and trailing spaces,
     * and normalizing internal spaces by replacing multiple spaces with a single space.
     *
     * @param string $datas The input string to be cleaned.
     * @return string The cleaned string.
     */
    private static function noSpace($datas)
    {

        $string = is_string($datas) ? $datas : (string) $datas;

        if (!extension_loaded('intl')) {
            throw new \Exception('The intl extension is required to use Normalizer.');
        }

        $string = \Normalizer::normalize($string, \Normalizer::FORM_D);

        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5);

        $string = preg_replace('/[^\x20-\x7E]/u', '', $string);

        $string = trim(preg_replace('/\s+/', ' ', $string));

        return $string;
    }

    /**
     * @return void
     */
    private static function forcingTokenVerification():void {
        
        static::initNamespace()['crsf']->toForceCrsf() === false ? static::class('errors')->error_403() : NULL;
    }
    
    /**
     * @return array|null
     */
    private static function forcingApiTokenVerification():array|null {

        if (static::initNamespace()['crsf']->toForceCrsf() === false) {
            static::initNamespace()['response']->JsonResponse(400, ['error' => "Method not found"]);
            die;
        } else {
            return NULL;
        }
    }   
       
    /**
     * Summary of isAssociativeArray
     * @param array $array
     * @return bool
     */
    private static function isAssociativeArray(
        array $array
    ): bool{
        return array_keys($array) !== range(0, count($array) - 1);
    }
}