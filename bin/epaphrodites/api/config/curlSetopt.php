<?php
namespace Epaphrodites\epaphrodites\api\config;

trait curlSetopt
{
    /**
     * Default headers for API requests
     */
    private const DEFAULT_HEADERS = [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: Epaphrodites-API-Client/1.0'
    ];

    /**
     * API request with custom host support
     *
     * @param string $path
     * @param array $data
     * @param string $method
     * @param bool $stream
     * @param array $usersHeaders
     * @param callable|null $streamCallback
     * @param string|null $customHost
     * @return array{data: mixed, error: bool, status: int|array{error: bool, message: string}}
     */
    protected static function request(
        string|null $path = null,
        array $data = [],
        string $method = 'POST',
        bool $stream = false,
        array $usersHeaders = [],
        ?callable $streamCallback = null,
        ?string $customHost = null,
    ): array {
        $headers = array_merge(self::DEFAULT_HEADERS, $usersHeaders);
        $ch = curl_init();

        // Build URL with custom host support
        $url = static::makePath($path, $customHost);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, !$stream);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Add headers if not empty
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Handle data for POST/PUT
        if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT'])) {
            $jsonData = json_encode($data);
            if ($jsonData === false) {
                curl_close($ch);
                return [
                    'error' => true,
                    'status' => 0,
                    'message' => 'Failed to encode data to JSON'
                ];
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }

        // Handle streaming
        if ($stream) {
            $streamData = [];
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($streamCallback, &$streamData) {
                $streamData[] = $data;
                if ($streamCallback && is_callable($streamCallback)) {
                    call_user_func($streamCallback, $data);
                }
                return strlen($data);
            });
        }

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            return [
                'error' => true,
                'status' => 0,
                'message' => "cURL error ($errno): $error"
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($stream) {
            return [
                'error' => $httpCode >= 400,
                'status' => $httpCode,
                'data' => $streamData ?? []
            ];
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'status' => $httpCode,
                'message' => 'Failed to decode JSON response: ' . json_last_error_msg()
            ];
        }

        return [
            'error' => $httpCode >= 400,
            'status' => $httpCode,
            'data' => $decodedResponse ?? []
        ];
    }

    /**
     * Streaming without cache (integrated with request system)
     *
     * @param mixed $stream
     * @param bool $withBuffering
     * @return string
     */
    protected static function streamChunks(
        mixed $stream,
        bool $withBuffering = true
    ): string {
        $output = '';
        
        // Handle boolean stream input
        if (is_bool($stream)) {
            if ($stream === true) {
                $withBuffering = true;
            }
            $stream = [];
        }

        // Handle array response from request function
        if (is_array($stream)) {
            // If it's a response array from request function
            if (isset($stream['data']) && isset($stream['error']) && isset($stream['status'])) {
                if ($stream['error']) {
                    return "Error: " . ($stream['message'] ?? 'Unknown error');
                }
                $stream = $stream['data'] ?? [];
            }
        }

        // Ensure we have an iterable
        if (!is_array($stream) && !($stream instanceof \Traversable)) {
            $stream = [];
        }

        // Set streaming headers
        if ($withBuffering && !headers_sent()) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
        }

        // Configure output buffering
        if ($withBuffering) {
            while (@ob_end_flush());
            ob_implicit_flush(true);
            set_time_limit(0);
            flush();
        }

        // Process chunks
        foreach ($stream as $chunk) {
            $escapedChunk = str_replace(["\n", "\r"], ['\\n', '\\r'], $chunk);
            $output .= $chunk . "\n";
            
            if ($withBuffering) {
                echo "$escapedChunk\n\n";
                flush();
                usleep(100000); // 100ms delay
            }
        }

        if ($withBuffering) {
            flush();
        }

        return $output;
    }

    /**
     * Build API path with custom host support
     *
     * @param string $path
     * @param string|null $customHost
     * @return string
     * @throws \RuntimeException if _PYTHON_SERVER_PORT_ is not defined and no custom host is provided
     */
    protected static function makePath(
        string $path,
        ?string $customHost = null,
    ): string {
        // Clean the path
        $path = ltrim($path, '/');

        // If custom host is provided, use it
        if ($customHost !== null && $path == null) {
            return $customHost;
        }

        // Otherwise, use default Python server configuration
        if (!defined('_PYTHON_SERVER_PORT_')) {
            throw new \RuntimeException('Python server port is not defined');
        }

        return "http://127.0.0.1:" . _PYTHON_SERVER_PORT_ . "/$path";
    }
}