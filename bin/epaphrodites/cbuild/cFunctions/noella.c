/*
===============================================================================
                                 MOTIVATION
===============================================================================

The "noella" module is a PHP extension written in C, created for the
Epaphrodites framework. Its purpose is to deliver focused, high-performance
features to PHP, leveraging the power of native code while maintaining a
developer-friendly interface.

This extension is motivated by the following goals:
  - Introduce lightweight, efficient functions for frequent tasks
  - Serve as a foundation for specialized logic required by the framework
  - Maintain simplicity and modularity for easy integration and evolution

"Noella" represents clarity and precision — bringing sharp, native tools
to the fingertips of PHP developers

===============================================================================
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "../config/noella/noella.h"

// ========================================================================
// ARGINFO
// ========================================================================
ZEND_BEGIN_ARG_INFO_EX(arginfo_noella_hello, 0, 0, 0)
ZEND_END_ARG_INFO()

// ========================================================================
// FUNCTION: HELLO
// ========================================================================
PHP_FUNCTION(hello_noella)
{
    php_printf("Hello from Noella extension!\n");
}

// ========================================================================
// FUNCTION MAPPING
// ========================================================================
static const zend_function_entry noella_functions[] = {
    PHP_FE(hello_noella, arginfo_noella_hello)
    PHP_FE_END
};

// ========================================================================
// MODULE ENTRY
// ========================================================================
zend_module_entry noella_module_entry = {
    STANDARD_MODULE_HEADER,
    "noella",                    // extension name
    noella_functions,            // Functions
    NULL,                        // MINIT
    NULL,                        // MSHUTDOWN
    NULL,                        // RINIT
    NULL,                        // RSHUTDOWN
    NULL,                        // MINFO
    NOELLA_VERSION,              // Version
    STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(noella)