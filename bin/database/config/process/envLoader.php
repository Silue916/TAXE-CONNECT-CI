<?php

declare(strict_types=1);

namespace Epaphrodites\database\config\process;

final class envLoader
{
    private const CONFIG_DIR = _DIR_CONFIG_INI_;
    private const INI_FILE = 'Config.ini';
    private const ENV_FILE = '.env';
    private const ENV_FILE_PERMISSIONS = 0600;
    private const SECTION_PATTERN = '/^(\d+)_CONFIGURATION$/';
    private const REQUIRED_KEYS = ['PORT', 'USER', 'PASSWORD', 'SOCKET_PATH'];

    private static bool $isLoaded = false;

    /**
     * Initialize the environment by loading or generating the .env file
     * @param bool $forceRegeneration Force regeneration of .env file
     * @throws \RuntimeException If file operations fail or configuration is invalid
     */
    public static function init(
        bool $forceRegeneration = false
    ): void{
        
        if (self::$isLoaded && !$forceRegeneration) {
            return;
        }

        try {

            if (!defined('_DIR_CONFIG_INI_') || !is_dir(self::CONFIG_DIR)) {
                throw new \RuntimeException("Configuration directory _DIR_CONFIG_INI_ is not defined or invalid");
            }

            $iniPath = self::buildPath(self::INI_FILE);
            $envPath = self::buildPath(self::ENV_FILE);

            if (!is_writable(self::CONFIG_DIR)&&_PRODUCTION_==false) {
                throw new \RuntimeException("Config directory is not writable: " . self::CONFIG_DIR);
            }            

            if ($forceRegeneration || !file_exists($envPath)) {
                self::generateEnvFromIni($iniPath, $envPath);
            }

            self::loadEnvFile($envPath);
            self::$isLoaded = true;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Environment initialization failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build file path with proper directory separator
     * @param string $file File name
     * @return string Full path to file
     */
    private static function buildPath(
        string $file
    ): string{

        return rtrim(self::CONFIG_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    }

    /**
     * Generate .env file from INI configuration
     * @param string $iniPath Path to INI file
     * @param string $envPath Path to .env file
     * @throws \RuntimeException If file operations or parsing fail
     * @throws \InvalidArgumentException If configuration values are invalid
     */
    private static function generateEnvFromIni(
        string $iniPath, 
        string $envPath
    ): void{

        if (!file_exists($iniPath) || !is_readable($iniPath)) {
            throw new \RuntimeException("INI file not found or unreadable: $iniPath");
        }

        $config = parse_ini_file($iniPath, true, INI_SCANNER_TYPED);
        if ($config === false) {
            throw new \RuntimeException("Unable to parse INI file: $iniPath");
        }

        $envContent = "# Auto-generated " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
        $hasValidConfig = false;
        $warnings = [];

        foreach ($config as $section => $settings) {
            if (!preg_match(self::SECTION_PATTERN, $section, $matches)) {
                $warnings[] = "Ignoring invalid section: $section";
                continue;
            }

            $index = $matches[1];

            foreach (self::REQUIRED_KEYS as $key) {
                if (!array_key_exists($key, $settings)) {
                    throw new \InvalidArgumentException("Missing required key '$key' in section [$section]");
                }
            }

            if (!empty($settings['PORT']) && (!is_numeric($settings['PORT']) || $settings['PORT'] < 0)) {
                throw new \InvalidArgumentException("Invalid PORT value in section [$section]: {$settings['PORT']}");
            }
            if (!empty($settings['DB_SOCKET_PATH']) && !file_exists($settings['DB_SOCKET_PATH'])) {
                $warnings[] = "DB_SOCKET_PATH may be invalid in section [$section]: {$settings['DB_SOCKET_PATH']}";
            }

            $envDriver = self::sanitize($settings['DRIVER'] ?? '');
            $envHost = self::sanitize($settings['HOST'] ?? '');
            $envDatabase = self::sanitize($settings['DATABASE'] ?? '');
            $envPort = self::sanitize($settings['PORT'] ?? '');
            $envUsers = self::sanitize($settings['USER'] ?? '');
            $envPassword = self::sanitize($settings['PASSWORD'] ?? '');
            $envSocket = self::sanitize($settings['DB_SOCKET_PATH'] ?? '');

            $envContent .= "# DB Configuration $index" . PHP_EOL;
            $envContent .= "{$index}DB_DRIVER=" . ($envDriver !== '' ? $envDriver : '""') . PHP_EOL;
            $envContent .= "{$index}DB_HOST=" . ($envHost !== '' ? $envHost : '""') . PHP_EOL;
            $envContent .= "{$index}DB_DATABASE=" . ($envDatabase !== '' ? $envDatabase : '""') . PHP_EOL;
            $envContent .= "{$index}DB_PORT=" . ($envPort !== '' ? $envPort : '""') . PHP_EOL;
            $envContent .= "{$index}DB_USER=" . ($envUsers !== '' ? $envUsers : '""') . PHP_EOL;
            $envContent .= "{$index}DB_PASSWORD=" . ($envPassword !== '' ? $envPassword : '""') . PHP_EOL;
            $envContent .= "{$index}DB_SOCKET_PATH=" . ($envSocket !== '' ? $envSocket : '""') . PHP_EOL . PHP_EOL;

            $hasValidConfig = true;
        }

        if (!$hasValidConfig) {
            throw new \RuntimeException("No valid configuration sections found in INI file: $iniPath");
        }

        if (!empty($warnings)) {
            error_log("EnvLoader warnings: " . implode('; ', $warnings));
        }

        $tempFile = $envPath . '.tmp';
        $handle = fopen($tempFile, 'w');
        if ($handle === false) {
            throw new \RuntimeException("Failed to create temporary file: $tempFile");
        }

        if (flock($handle, LOCK_EX)) {
            if (fwrite($handle, $envContent) === false) {
                flock($handle, LOCK_UN);
                fclose($handle);
                @unlink($tempFile);
                throw new \RuntimeException("Failed to write to temporary file: $tempFile");
            }
            flock($handle, LOCK_UN);
            fclose($handle);

            if (!rename($tempFile, $envPath)) {
                @unlink($tempFile);
                throw new \RuntimeException("Failed to move temporary file to: $envPath");
            }

            if (!chmod($envPath, self::ENV_FILE_PERMISSIONS)) {
                error_log("Warning: Failed to set permissions on: $envPath");
            }
        } else {
            fclose($handle);
            @unlink($tempFile);
            throw new \RuntimeException("Failed to acquire lock on temporary file: $tempFile");
        }
    }

    /**
     * Load environment variables from .env file
     * @param string $envPath Path to .env file
     * @throws \RuntimeException If file operations fail
     */
    private static function loadEnvFile(
        string $envPath
    ): void{

        if (!file_exists($envPath) || !is_readable($envPath)) {
            throw new \RuntimeException(".env file not found or not readable: $envPath");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new \RuntimeException("Failed to read .env file: $envPath");
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if ($key === '') {
                continue;
            }

            if (getenv($key) !== false || isset($_ENV[$key]) || isset($_SERVER[$key])) {
                continue;
            }

            $value = self::parseValue($value);

            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Sanitize input values
     * @param mixed $value Value to sanitize
     * @return string Sanitized string
     */
    private static function sanitize(
        mixed $value
    ): string{
        if ($value === null || $value === '') {
            return '';
        }

        $value = (string) $value;

        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);

        $value = str_replace(["\r", "\n", '"', "'"], ['\\r', '\\n', '\"', "\'"], $value);

        return trim($value);
    }

    /**
     * Parse .env values, handling quotes and escaped characters
     * @param string $value Raw value from .env
     * @return string Parsed value
     */
    private static function parseValue(
        string $value
    ): string{
        
        if (preg_match('/^[\'"]?(.*?)[\'"]?$/', $value, $matches)) {
            $value = $matches[1];
        }

        $value = str_replace(['\"', "\'"], ['"', "'"], $value);

        return $value;
    }
}