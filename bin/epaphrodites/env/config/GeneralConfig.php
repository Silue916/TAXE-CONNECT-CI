<?php

namespace Epaphrodites\epaphrodites\env\config;

use Epaphrodites\database\datas\arrays\ApiStaticKeygen;

class GeneralConfig extends ApiStaticKeygen
{

    /**
     * @param mixed $Files
     * @param mixed $divid
     * @return string
     */
    public function EndFiles(
        $Files, 
        $divid
    )
    {

        $Files = explode($divid, $Files);

        return end($Files);
    }

    /**
     * @return string
     */
    public static function methods()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param string|null $key
     * @param string|null $values
     * @return void
     */
    public function SetSession(
        ?string $key = null, 
        ?string $values = null
    )
    {

        return $_SESSION[$key] = $values;
    }

    /**
     * @param string|null $key
     * @return void
     */
    public function GetSessions(
        ?string $key = null
    )
    {

        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    }

    /**
     * @return string
     */
    public function GetFiles($key)
    {

        return $_FILES[array_keys($_FILES)[$key]]['tmp_name'];
    }

    public function FilesArray(): array
    {
        return array_keys($_FILES);
    }

    /**
     * execute Python
     * 
     * @param mixed $scriptPath
     * @param array $data
     * @param mixed $onData
     * @param bool $useStreaming
     * @param int $timeout
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return mixed
     */
    public function pythonSystemCode(
        ?string $scriptPath = null,
        array $data = [],
        ?callable $onData = null,
        bool $useStreaming = false,
        int $timeout = 3600
    ): mixed {
    
        if (empty($scriptPath) || !is_file($scriptPath) || !is_readable($scriptPath)) {
            throw new \InvalidArgumentException("Invalid or inaccessible script path: " . ($scriptPath ?? 'null'));
        }
    
        $processedData = $this->sanitizeData($data);
        
        if (!isset($processedData['timeout'])) {
            $processedData['timeout'] = $timeout;
        }
        
        $encodedData = base64_encode(json_encode($processedData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    
        $scriptPath = escapeshellarg($scriptPath);
        $encodedData = escapeshellarg($encodedData);
    
        $command = sprintf('%s -u %s %s 2>&1', escapeshellcmd(_PYTHON_), $scriptPath, $encodedData);
    
        $descriptorSpec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w']  // stderr
        ];
    
        $process = proc_open($command, $descriptorSpec, $pipes);
        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start Python process');
        }
    
        try {
            fclose($pipes[0]);
    
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
    
            $output = '';
            $errors = '';
            
            $phpTimeout = $timeout + 5; 
            $startTime = time();
            
            while (true) {
                $read = [$pipes[1], $pipes[2]];
                $write = $except = null;
    
                if (time() - $startTime > $phpTimeout) {
                    proc_terminate($process, 9);
                    throw new \RuntimeException("PHP timeout after {$phpTimeout} seconds. Python process terminated.");
                }
    
                $numChanged = stream_select($read, $write, $except, 1);
    
                if ($numChanged === false) {
                    throw new \RuntimeException('Stream select failed');
                }
    
                if ($numChanged === 0) {
                    continue;
                }
    
                foreach ($read as $stream) {
                    $content = fread($stream, 8192);
                    if ($content === false || ($content === '' && feof($stream))) {
                        continue;
                    }
    
                    if ($stream === $pipes[1]) {
                        if (!$useStreaming) {
                            $output .= $content;
                        }
                        if ($onData !== null && is_callable($onData)) {
                            call_user_func($onData, $content);
                        }
                    } else {
                        $errors .= $content;
                    }
                }
    
                $status = proc_get_status($process);
                if (!$status['running']) {
                    break;
                }
            }
    
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);
    
            if ($returnCode !== 0) {
                $jsonOutput = json_decode($output, true);
                if (is_array($jsonOutput) && isset($jsonOutput['status']) && $jsonOutput['status'] === 'error') {
                    $errorMessage = $jsonOutput['error'] ?? 'Unknown error';
                    throw new \RuntimeException(sprintf(
                        "Python script returned error: %s",
                        $errorMessage
                    ));
                } else {
                    throw new \RuntimeException(sprintf(
                        "Python script execution failed with code %d: %s",
                        $returnCode,
                        trim($errors ?: $output)
                    ));
                }
            }
    
            if ($useStreaming) {
                return true; // ou null, ou tout autre valeur neutre si stream
            }
    
            $output = trim($output);
            if ($this->isBase64($output)) {
                $decoded = base64_decode($output, true);
                if ($decoded !== false) {
                    $output = $decoded;
                }
            }
    
            $jsonOutput = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonOutput;
            }
    
            return $output;
    
        } catch (\Exception $e) {
            if (isset($pipes[1]) && is_resource($pipes[1])) fclose($pipes[1]);
            if (isset($pipes[2]) && is_resource($pipes[2])) fclose($pipes[2]);
            if (isset($process) && is_resource($process)) proc_close($process);
            throw $e;
        }
    }
    
    private function sanitizeData(
        array $data
    ): array {
        $result = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->sanitizeData($value);
            } elseif (is_string($value) && $this->isBinary($value)) {
                $result[$key] = [
                    '_type' => 'binary',
                    'data' => base64_encode($value)
                ];
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    private function isBinary(
        string $str
    ): bool {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }
    
    private function isBase64(
        string $str
    ): bool {
        if (!preg_match('/^[a-zA-Z0-9\/+]+={0,2}$/', $str)) {
            return false;
        }
        return base64_encode(base64_decode($str, true)) === $str;
    }
}