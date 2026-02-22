<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\settingForEnvManagers;
use RuntimeException;
use InvalidArgumentException;

class modelForEnvManagers extends settingForEnvManagers
{
    private const CONFIG_DIR = _DIR_CONFIG_INI_;
    private const INI_FILE = 'Config.ini';
    private const ENV_FILE = '.env';
    private const VALID_SECTION_PATTERN = '/^(\d+)_CONFIGURATION$/';
    private const REQUIRED_KEYS = ['HOST', 'USER', 'PASSWORD'];

    /**
     * Executes the console command to generate or load the .env file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Command status (SUCCESS or FAILURE)
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $iniPath = $this->buildPath(self::INI_FILE);
            $envPath = $this->buildPath(self::ENV_FILE);
            $load = (bool)($input->getOption('load') ?? false);
            $generate = (bool)($input->getOption('generate') ?? false);

            $this->ensureDirectoryExists($output);

            if (!$this->isFileReadable($iniPath)) {
                $output->writeln("<error>Configuration file not found or not readable: $iniPath</error>");
                return static::FAILURE;
            }

            if (!$generate && !file_exists($envPath)) {
                $output->writeln("<comment>.env file not found. Generating automatically...</comment>");
                $result = $this->generateEnvFile($iniPath, $envPath, $output);
                if ($result === static::FAILURE) {
                    return static::FAILURE;
                }
            }

            return $generate
                ? $this->generateEnvFile($iniPath, $envPath, $output)
                : $this->loadEnvFile($envPath, $output);
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                '<error>Failed to execute command: %s</error>',
                $e->getMessage()
            ));
            return static::FAILURE;
        }
    }

    /**
     * Builds file path with proper directory separator
     *
     * @param string $file
     * @return string
     */
    private function buildPath(string $file): string
    {
        return rtrim(self::CONFIG_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Ensures configuration directory exists
     *
     * @param OutputInterface $output
     * @throws RuntimeException
     * @return void
     */
    private function ensureDirectoryExists(OutputInterface $output): void
    {
        if (!is_dir(self::CONFIG_DIR) && !mkdir(self::CONFIG_DIR, 0755, true)) {
            $output->writeln("<error>Failed to create configuration directory: " . self::CONFIG_DIR . "</error>");
            throw new RuntimeException('Failed to create configuration directory: ' . self::CONFIG_DIR);
        }
    }

    /**
     * Checks if file exists and is readable
     *
     * @param string $path
     * @return bool
     */
    private function isFileReadable(string $path): bool
    {
        return file_exists($path) && is_readable($path);
    }

    /**
     * Generates a .env file from the provided .ini configuration file
     *
     * @param string $iniPath
     * @param string $envPath
     * @param OutputInterface $output
     * @return int Command status (SUCCESS or FAILURE)
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    private function generateEnvFile(
        string $iniPath,
        string $envPath,
        OutputInterface $output
    ): int {
        if (!$this->isFileReadable($iniPath)) {
            $output->writeln("<error>Configuration file is not readable</error>");
            return static::FAILURE;
        }

        $config = $this->parseIniFile($iniPath, $output);
        if ($config === false) {
            return static::FAILURE;
        }

        $envContent = $this->generateEnvContent($config);
        $message = file_exists($envPath)
            ? "<comment>.env file already exists. Overwriting...</comment>"
            : "<info>Creating .env file...</info>";
        $output->writeln($message);

        if (file_put_contents($envPath, $envContent) === false) {
            $output->writeln("<error>Failed to write .env file</error>");
            return static::FAILURE;
        }

        chmod($envPath, 0600); // Secure file permissions
        $output->writeln("<info>.env file successfully generated</info>");
        return static::SUCCESS;
    }

    /**
     * Parses INI file with validation
     *
     * @param string $iniPath
     * @param OutputInterface $output
     * @return array|false
     */
    private function parseIniFile(string $iniPath, OutputInterface $output): array|bool
    {
        $config = parse_ini_file($iniPath, true, INI_SCANNER_TYPED);
        if ($config === false) {
            $output->writeln("<error>Failed to parse configuration file</error>");
            return false;
        }

        foreach ($config as $section => $settings) {
            if (preg_match(self::VALID_SECTION_PATTERN, $section)) {
                if (!$this->validateSection($section, $settings, $output)) {
                    return false;
                }
            }
        }

        return $config;
    }

    /**
     * Validates configuration section
     *
     * @param string $section
     * @param array $settings
     * @param OutputInterface $output
     * @return bool
     */
    private function validateSection(string $section, array $settings, OutputInterface $output): bool
    {
        foreach (self::REQUIRED_KEYS as $key) {
            if (!isset($settings[$key])) {
                $output->writeln(
                    sprintf('<error>Missing required key "%s" in section "%s"</error>', $key, $section)
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Generates .env content from configuration
     *
     * @param array $config
     * @return string
     */
    private function generateEnvContent(array $config): string
    {
        $envContent = "# Generated .env file\n\n";

        foreach ($config as $section => $settings) {
            if (!preg_match(self::VALID_SECTION_PATTERN, $section, $matches)) {
                continue;
            }

            $index = $matches[1];
            $envContent .= sprintf("# Database configuration %s\n", $index);
            $envContent .= sprintf("%sDB_HOST=%s\n", $index, $this->sanitizeValue($settings['HOST'] ?? ''));
            $envContent .= sprintf("%sDB_DRIVER=%s\n", $index, $this->sanitizeValue($settings['DRIVER'] ?? ''));
            $envContent .= sprintf("%sDB_DATABASE=%s\n", $index, $this->sanitizeValue($settings['DATABASE'] ?? ''));
            $envContent .= sprintf("%sDB_PORT=%s\n", $index, $this->sanitizeValue($settings['PORT'] ?? ''));
            $envContent .= sprintf("%sDB_USER=%s\n", $index, $this->sanitizeValue($settings['USER'] ?? ''));
            $envContent .= sprintf("%sDB_PASSWORD=%s\n", $index, $this->sanitizeValue($settings['PASSWORD'] ?? ''));
            $envContent .= sprintf("%sDB_SOCKET_PATH=%s\n\n", $index, $this->sanitizeValue($settings['SOCKET_PATH'] ?? ''));
        }

        return $envContent;
    }

    /**
     * Sanitizes values for .env file
     *
     * @param mixed $value
     * @return string
     */
    private function sanitizeValue($value): string
    {
        $value = (string)$value;
        $value = str_replace(['"', "'"], ['\"', "\'"], $value);
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    }

    /**
     * Loads environment variables from the .env file
     *
     * @param string $envPath
     * @param OutputInterface $output
     * @return int Command status (SUCCESS or FAILURE)
     */
    private function loadEnvFile(
        string $envPath,
        OutputInterface $output
    ): int {
        if (!$this->isFileReadable($envPath)) {
            $output->writeln("<error>.env file not found or not readable</error>");
            return static::FAILURE;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            $output->writeln("<error>Failed to read .env file</error>");
            return static::FAILURE;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                $output->writeln("<comment>Skipping invalid line</comment>");
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                $output->writeln("<comment>Skipping empty key in line</comment>");
                continue;
            }

            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        $output->writeln("<info>Environment variables loaded successfully</info>");
        return static::SUCCESS;
    }
}