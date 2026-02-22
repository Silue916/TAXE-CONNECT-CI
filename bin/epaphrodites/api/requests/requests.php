<?php
namespace Epaphrodites\epaphrodites\api\requests;
use Epaphrodites\epaphrodites\api\config\curlSetopt;

class requests
{
    use curlSetopt;

    /**
     * API request with streaming support (simplified)
     *
     * @param string $path
     * @param array $data
     * @param bool $withBuffering
     * @return string
     */
    public static function pyStream(
        string $path,
        array $data = [],
        bool $withBuffering = true
    ): string {
        return static::streamChunks(
            static::request(
                path: $path,
                data: $data,
                method: 'POST',
                stream: true,
                usersHeaders: [],
                streamCallback: null
            ),
            $withBuffering
        );
    }

    /**
     * Generic API request for Python server (fixed host)
     *
     * @param string $path
     * @param array $data
     * @param string $method
     * @param bool $stream
     * @param array $usersHeaders
     * @param callable|null $onChunk
     * @return array{data: mixed, error: bool, status: int|array{error: bool, message: string}}
     */
    public static function pyGet(
        string $path,
        array $data = [],
        string $method = 'POST',
        bool $stream = false,
        array $usersHeaders = [],
        ?callable $onChunk = null
    ): array {
        return static::request(
            path: $path,
            data: $data,
            method: $method,
            stream: $stream,
            usersHeaders: $usersHeaders,
            streamCallback: $onChunk
        );
    }

    /**
     * Generic API request with custom host support and token authentication
     *
     * @param string $path
     * @param array $data
     * @param string $method
     * @param bool $stream
     * @param array $usersHeaders
     * @param callable|null $onChunk
     * @param string|null $customHost
     * @param string|null $token
     * @param string $tokenType
     * @return array{data: mixed, error: bool, status: int|array{error: bool, message: string}}
     */
    public static function get(
        string|null $path = null,
        array $data = [],
        string $method = 'POST',
        bool $stream = false,
        array $usersHeaders = [],
        ?callable $onChunk = null,
        ?string $host = null,
        ?string $token = null,
        string $tokenType = 'Bearer'
    ): array {
       
        $headers = $usersHeaders;
        if ($token !== null) {
            $headers[] = "Authorization: {$tokenType} {$token}";
        }

        return static::request(
            path: $path,
            data: $data,
            method: $method,
            stream: $stream,
            usersHeaders: $headers,
            streamCallback: $onChunk,
            customHost: $host
        );
    }
}