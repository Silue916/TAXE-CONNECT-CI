<?php

class InstallComponent
{
    private array $requiredExtensions = [
        'openssl',
        'zip',
        'fileinfo',
        'gd',
        'intl',
        'pdo',
        'json',
        'xml',
        'mbstring',
        'pdo_sqlite',
    ];

    public function run(): void
    {
        if (!$this->isPhpInstalled()) {
            echo "\033[31mPHP is not installed or not found in PATH ❌\033[0m" . PHP_EOL;
            return;
        }

        if (!$this->isVersionCompatible()) {
            echo "\033[31mPHP version must be >= 8.1 ❌ (current: " . PHP_VERSION . ")\033[0m" . PHP_EOL;
            return;
        }

        $os = php_uname('s');

        match (true) {
            str_contains($os, 'Windows') => $this->installExtensionsOnWindows(),
            str_contains($os, 'Linux') => $this->installExtensionsOnLinux(),
            str_contains($os, 'Darwin') => $this->installExtensionsOnMacOS(),
            default => $this->handleUnsupportedOS($os),
        };

        $this->updateComposer();
        $this->updateAutoload();
        
    }

    private function isPhpInstalled(): bool
    {
        $phpVersion = shell_exec('php -v');
        return is_string($phpVersion) && str_starts_with(trim($phpVersion), 'PHP');
    }

    private function isVersionCompatible(): bool
    {
        return version_compare(PHP_VERSION, '8.2', '>=');
    }

    private function isExtensionLoaded(
        string $extension
    ): bool{
        return extension_loaded($extension);
    }

    private function handleUnsupportedOS(
        string $os
    ): void{
        echo "Unsupported OS: $os" . PHP_EOL;
    }

    private function installExtensionsOnLinux(): void
    {
        echo "Checking extensions on Linux..." . PHP_EOL;

        $manager = $this->detectPackageManager();
        if (!$manager) {
            echo "\033[31mNo supported package manager found ❌\033[0m" . PHP_EOL;
            return;
        }

        $pkgPrefix = match ($manager) {
            'apt-get', 'dnf', 'yum', 'pacman' => 'php-',
            default => 'php-'
        };

        foreach ($this->requiredExtensions as $ext) {
            if ($this->isExtensionLoaded($ext)) {
                echo "\033[34m$ext .................................. already loaded ✅\033[0m" . PHP_EOL;
                continue;
            }

            $package = $pkgPrefix . str_replace('_', '-', $ext);
            $cmd = $this->getInstallCommand($manager, [$package]);
            if ($cmd) {
                $this->executeCommand($cmd);
                echo "\033[32mInstalled $ext ✅\033[0m" . PHP_EOL;
            }
        }
    }

    private function installExtensionsOnMacOS(): void
    {
        echo "Checking extensions on macOS..." . PHP_EOL;

        foreach ($this->requiredExtensions as $ext) {
            if ($this->isExtensionLoaded($ext)) {
                echo "\033[34m$ext .................................. already loaded ✅\033[0m" . PHP_EOL;
                continue;
            }

            echo "\033[33m$ext not loaded – please install manually or via pecl/brew ⚠️\033[0m" . PHP_EOL;
        }
    }

    private function installExtensionsOnWindows(): void
    {
        echo "Checking extensions on Windows..." . PHP_EOL;
    
        $phpIniPath = php_ini_loaded_file();
        if (!$phpIniPath || !file_exists($phpIniPath)) {
            echo "\033[31mUnable to locate php.ini ❌\033[0m" . PHP_EOL;
            return;
        }
    
        $iniContent = file_get_contents($phpIniPath);
        $iniLines = explode("\n", $iniContent);
        $modified = false;
    
        foreach ($this->requiredExtensions as $extension) {
            if ($this->isExtensionLoaded($extension)) {
                echo "\033[34m$extension .................................. already loaded ✅\033[0m" . PHP_EOL;
                continue;
            }
    
            $pattern = "/^\s*;?\s*extension\s*=\s*($extension|$extension\.dll)\b/i";
            $found = false;
    
            foreach ($iniLines as $i => $line) {
                if (preg_match($pattern, $line)) {
                    if (str_starts_with(trim($line), ';')) {
                        
                        $iniLines[$i] = preg_replace('/^\s*;\s*/', '', $line);
                        echo "\033[32m$extension .................................. uncommented ✅\033[0m" . PHP_EOL;
                        $modified = true;
                    } else {
                        echo "\033[33m$extension already declared in php.ini ✅\033[0m" . PHP_EOL;
                    }
                    $found = true;
                    break;
                }
            }
    
            if (!$found) {
                $iniLines[] = "extension=$extension";
                echo "\033[32m$extension .................................. added to php.ini ✅\033[0m" . PHP_EOL;
                $modified = true;
            }
        }
    
        if ($modified) {
            file_put_contents($phpIniPath, implode("\n", $iniLines));
            echo "\033[32mphp.ini updated ✅\033[0m" . PHP_EOL;
        } else {
            echo "\033[34mNo changes needed to php.ini 💤\033[0m" . PHP_EOL;
        }
    }
    

    private function detectPackageManager(): ?string
    {
        foreach (['apt-get', 'dnf', 'yum', 'pacman'] as $manager) {
            if (exec("command -v $manager")) return $manager;
        }
        return null;
    }

    private function getInstallCommand(
        string $manager, 
        array $packages
    ): ?string{
        return match ($manager) {
            'apt-get' => 'sudo apt-get install -y ' . implode(' ', $packages),
            'dnf' => 'sudo dnf install -y ' . implode(' ', $packages),
            'yum' => 'sudo yum install -y ' . implode(' ', $packages),
            'pacman' => 'sudo pacman -S --noconfirm ' . implode(' ', $packages),
            default => null
        };
    }

    private function executeCommand(
        string $cmd
    ): void{
        echo "Running: $cmd" . PHP_EOL;
        passthru($cmd);
    }

    private function updateAutoload(): void
    {
        echo "Composer autoload..." . PHP_EOL;
        $this->executeCommand("composer dump-autoload");
        echo "\033[32mAutoload .................................. updated ✅\033[0m" . PHP_EOL;
    }

    private function updateComposer(): void
    {
        echo "Updating Composer..." . PHP_EOL;
        $this->executeCommand("composer update");
        echo "\033[32mComposer .................................. updated ✅\033[0m" . PHP_EOL;
    }
}