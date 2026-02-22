<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

use Symfony\Component\Console\Output\OutputInterface;

class ExtensionBuilderService
{
    public function build(string $extensionName, OutputInterface $output): void
    {
        $upperName = strtoupper($extensionName);
        $this->createConfigDirectory($extensionName, $output);
        $this->createConfigM4($extensionName, $upperName, $output);
        $this->createHeaderAndCFiles($extensionName, $upperName, $output);
    }

    private function createConfigDirectory(string $extensionName, OutputInterface $output): void
    {
        $configDir = "bin/epaphrodites/cbuild/config/{$extensionName}";
        if (!is_dir($configDir)) {
            mkdir($configDir, 0777, true);
            $output->writeln("<info>✅ Directory '{$extensionName}' has been created successfully</info>");
        } else {
            $output->writeln("<comment>❌<fg=red> '{$extensionName}' already exists for this extension </fg=red></comment>");
        }
    }

    private function createConfigM4(string $extensionName, string $upperName, OutputInterface $output): void
    {
        $configPath = "bin/epaphrodites/cbuild/config/{$extensionName}/config.m4";

        $content = <<<EOT
PHP_ARG_ENABLE({$extensionName}, whether to enable the {$extensionName} extension,
[  --enable-{$extensionName}      Enable {$extensionName} extension support])

if test "\$PHP_{$upperName}" != "no"; then
    AC_DEFINE(HAVE_{$upperName}, 1, [Have {$extensionName} extension])
    PHP_NEW_EXTENSION({$extensionName}, ../cFunctions/{$extensionName}.c, \$ext_shared)
    PHP_ADD_INCLUDE([../config/{$extensionName}])
fi
EOT;

        if (!file_exists($configPath)) {
            file_put_contents($configPath, $content);
            $output->writeln("<info>✅ 'config.m4' has been created successfully</info>");
        } else {
            $output->writeln("<comment>❌<fg=red> 'config.m4' already exists for this extension </fg=red></comment>");
        }
    }

    private function createHeaderAndCFiles(string $extensionName, string $upperName, OutputInterface $output): void
    {
        $configDir = "bin/epaphrodites/cbuild/config/{$extensionName}";
        $headerPath = "{$configDir}/{$extensionName}.h";
        $cFunctionsDir = "bin/epaphrodites/cbuild/cFunctions";
        $cFilePath = "{$cFunctionsDir}/{$extensionName}.c";

        // .h file
        $header = <<<EOT
#ifndef PHP_{$upperName}_H
#define PHP_{$upperName}_H

#define {$upperName}_VERSION "0.1"
#define PHP_{$upperName}_EXTNAME "{$extensionName}"

extern zend_module_entry {$extensionName}_module_entry;
#define phpext_{$extensionName}_ptr &{$extensionName}_module_entry

PHP_FUNCTION(hello_{$extensionName});

#endif /* PHP_{$upperName}_H */
EOT;

        if (!file_exists($headerPath)) {
            file_put_contents($headerPath, $header);
            $output->writeln("<info>✅ '{$extensionName}.h' has been created successfully");
        } else {
            $output->writeln("<comment>❌<fg=red> '{$extensionName}.h' File already exists for this extension </fg=red></comment>");
        }

        // .c file
        if (!is_dir($cFunctionsDir)) {
            mkdir($cFunctionsDir, 0777, true);
        }

        $cFile = <<<EOT
/*
===============================================================================
                                 MOTIVATION
===============================================================================

The "{$extensionName}" module is a PHP extension written in C, created for the
Epaphrodites framework. Its purpose is to deliver focused, high-performance
features to PHP, leveraging the power of native code while maintaining a
developer-friendly interface.

This extension is motivated by the following goals:
  - Introduce lightweight, efficient functions for frequent tasks
  - Serve as a foundation for specialized logic required by the framework
  - Maintain simplicity and modularity for easy integration and evolution

"{$extensionName}" represents clarity and precision — bringing sharp, native tools
to the fingertips of PHP developers

===============================================================================
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "../config/{$extensionName}/{$extensionName}.h"

ZEND_BEGIN_ARG_INFO_EX(arginfo_{$extensionName}_hello, 0, 0, 0)
ZEND_END_ARG_INFO()

PHP_FUNCTION(hello_{$extensionName})
{
    php_printf("Hello from {$extensionName} extension!\\n");
}

static const zend_function_entry {$extensionName}_functions[] = {
    PHP_FE(hello_{$extensionName}, arginfo_{$extensionName}_hello)
    PHP_FE_END
};

zend_module_entry {$extensionName}_module_entry = {
    STANDARD_MODULE_HEADER,
    "{$extensionName}",
    {$extensionName}_functions,
    NULL, NULL, NULL, NULL, NULL,
    {$upperName}_VERSION,
    STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE({$extensionName})
EOT;

        if (!file_exists($cFilePath)) {
            file_put_contents($cFilePath, $cFile);
            $output->writeln("<info>✅ '{$extensionName}.c' has been created successfully</info>");
        } else {
            $output->writeln("<comment>❌<fg=red> '{$extensionName}.c' already exists for this extension </fg=red></comment>");
        }
    }
}