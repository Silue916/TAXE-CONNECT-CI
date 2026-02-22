#ifndef PHP_SHLOMO_H
#define PHP_SHLOMO_H

extern zend_module_entry shlomo_module_entry;
#define phpext_shlomo_ptr &shlomo_module_entry
#define PHP_SHLOMO_VERSION "0.2"

#ifdef ZTS
#include "TSRM.h"
#endif

ZEND_BEGIN_MODULE_GLOBALS(shlomo)
ZEND_END_MODULE_GLOBALS(shlomo)

#ifdef ZTS
#define SHLOMO_G(v) TSRMG(shlomo_globals_id, zend_shlomo_globals *, v)
#else
#define SHLOMO_G(v) (shlomo_globals.v)
#endif

// Declare PHP-exposed functions
PHP_FUNCTION(shlomo_hello);
PHP_FUNCTION(shlomo_async);
PHP_FUNCTION(shlomo_await);
PHP_FUNCTION(shlomo_then);
PHP_FUNCTION(shlomo_task_info);

#endif
