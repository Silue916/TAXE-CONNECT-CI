/*
===============================================================================
                                 MOTIVATION
===============================================================================

The "shlomo" module is a PHP extension written in C, designed for the
Epaphrodites framework. Its goal is to bring native power to PHP: raw
performance, real multitasking, and low-level capabilities, all while keeping
a clean and simple interface for PHP developers.

This extension is motivated by three core principles:
  - Speed up critical operations using native C functions
  - Enable real asynchronous execution (via pthreads) from PHP
  - Provide a modular, extensible base for future framework needs

"Shlomo" means "peace" — symbolizing the harmony between high-level PHP
and low-level C.

===============================================================================
*/

#include "php.h"
#include "../config/shlomo/shlomo.h"
#include "zend_exceptions.h"
#include "zend_API.h"
#include "zend_types.h"
#include "ext/standard/info.h"
#include <pthread.h>
#include <sys/stat.h>
#include <stdlib.h>

// ========================================================================
// ARGINFO
// ========================================================================

ZEND_BEGIN_ARG_INFO_EX(arginfo_shlomo_hello, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_shlomo_async, 0, 0, 1)
    ZEND_ARG_CALLABLE_INFO(0, func, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_shlomo_await, 0, 0, 1)
    ZEND_ARG_INFO(0, promise)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_shlomo_then, 0, 0, 2)
    ZEND_ARG_INFO(0, promise)
    ZEND_ARG_CALLABLE_INFO(0, callback, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_shlomo_task_info, 0, 0, 2)
    ZEND_ARG_TYPE_INFO(0, js_code, IS_STRING, 0)
    ZEND_ARG_TYPE_INFO(0, delay, IS_LONG, 0)
ZEND_END_ARG_INFO()

// ========================================================================
// STRUCTURES
// ========================================================================

typedef struct {
    pthread_t thread;
    zval callback;
    zval result;
    zend_bool finished;
    zval then_callback;
    #ifdef ZTS
    void *tsrm_ls;
    #endif
} shlomo_promise_t;

static int le_shlomo_promise;

static void shlomo_promise_dtor(zend_resource *rsrc)
{
    shlomo_promise_t *promise = (shlomo_promise_t *)rsrc->ptr;
    
    if (!promise->finished) {
        pthread_join(promise->thread, NULL);
    }
    
    zval_ptr_dtor(&promise->callback);
    zval_ptr_dtor(&promise->result);
    
    if (!Z_ISUNDEF(promise->then_callback)) {
        zval_ptr_dtor(&promise->then_callback);
    }
    
    efree(promise);
}

// ========================================================================
// THREAD FUNCTION
// ========================================================================

void* shlomo_async_runner(void* data) {
    shlomo_promise_t* promise = (shlomo_promise_t*)data;
    
    #ifdef ZTS
    void *tsrm_ls = promise->tsrm_ls;
    #endif
    
    zval retval;
    ZVAL_UNDEF(&retval);

    if (call_user_function(EG(function_table), NULL, &promise->callback, &retval, 0, NULL) == SUCCESS) {
        ZVAL_COPY(&promise->result, &retval);
        zval_ptr_dtor(&retval);
    } else {
        ZVAL_STRING(&promise->result, "Async call failed.");
    }

    promise->finished = 1;

    if (!Z_ISUNDEF(promise->then_callback)) {
        zval params[1];
        ZVAL_COPY(&params[0], &promise->result);
        zval dummy;
        ZVAL_UNDEF(&dummy);
        call_user_function(EG(function_table), NULL, &promise->then_callback, &dummy, 1, params);
        zval_ptr_dtor(&params[0]);
        zval_ptr_dtor(&dummy);
    }

    return NULL;
}

// ========================================================================
// FUNCTION: ASYNC
// ========================================================================

PHP_FUNCTION(shlomo_async)
{
    zval* callable;

    ZEND_PARSE_PARAMETERS_START(1, 1)
        Z_PARAM_ZVAL(callable)
    ZEND_PARSE_PARAMETERS_END();

    if (!zend_is_callable(callable, 0, NULL)) {
        zend_throw_exception(NULL, "Argument must be callable", 0);
        RETURN_FALSE;
    }

    shlomo_promise_t* promise = emalloc(sizeof(shlomo_promise_t));
    promise->finished = 0;
    ZVAL_COPY(&promise->callback, callable);
    ZVAL_UNDEF(&promise->result);
    ZVAL_UNDEF(&promise->then_callback);
    
    #ifdef ZTS
    promise->tsrm_ls = tsrm_ls;
    #endif

    if (pthread_create(&promise->thread, NULL, shlomo_async_runner, promise) != 0) {
        zend_throw_exception(NULL, "Unable to create async thread", 0);
        zval_ptr_dtor(&promise->callback);
        efree(promise);
        RETURN_FALSE;
    }

    RETURN_RES(zend_register_resource(promise, le_shlomo_promise));
}

// ========================================================================
// FUNCTION: AWAIT
// ========================================================================

PHP_FUNCTION(shlomo_await)
{
    zval* res;
    shlomo_promise_t* promise;
    
    ZEND_PARSE_PARAMETERS_START(1, 1)
        Z_PARAM_RESOURCE(res)
    ZEND_PARSE_PARAMETERS_END();

    promise = (shlomo_promise_t*)zend_fetch_resource(Z_RES_P(res), "ShlomoPromise", le_shlomo_promise);
    
    if (promise == NULL) {
        RETURN_FALSE;
    }

    if (!promise->finished) {
        pthread_join(promise->thread, NULL);
        promise->finished = 1;
    }

    RETURN_ZVAL(&promise->result, 1, 0);
}

// ========================================================================
// FUNCTION: THEN
// ========================================================================

PHP_FUNCTION(shlomo_then)
{
    zval* res;
    zval* callback;
    shlomo_promise_t* promise;

    ZEND_PARSE_PARAMETERS_START(2, 2)
        Z_PARAM_RESOURCE(res)
        Z_PARAM_ZVAL(callback)
    ZEND_PARSE_PARAMETERS_END();

    promise = (shlomo_promise_t*)zend_fetch_resource(Z_RES_P(res), "ShlomoPromise", le_shlomo_promise);
    
    if (promise == NULL) {
        RETURN_FALSE;
    }

    if (!zend_is_callable(callback, 0, NULL)) {
        zend_throw_exception(NULL, "Second argument must be callable", 0);
        RETURN_FALSE;
    }

    ZVAL_COPY(&promise->then_callback, callback);

    if (promise->finished) {
        zval params[1];
        ZVAL_COPY(&params[0], &promise->result);
        zval dummy;
        ZVAL_UNDEF(&dummy);
        call_user_function(EG(function_table), NULL, &promise->then_callback, &dummy, 1, params);
        zval_ptr_dtor(&params[0]);
        zval_ptr_dtor(&dummy);
    }

    RETURN_TRUE;
}

// ========================================================================
// FUNCTION: SCHEDULE CLIENT TASK (HTML OUTPUT)
// ========================================================================

PHP_FUNCTION(shlomo_task_info)
{
    char* js_code;
    size_t js_code_len;
    zend_long delay;

    ZEND_PARSE_PARAMETERS_START(2, 2)
        Z_PARAM_STRING(js_code, js_code_len)
        Z_PARAM_LONG(delay)
    ZEND_PARSE_PARAMETERS_END();

    php_printf("<script>setTimeout(function(){%s}, %lld);</script>\n", js_code, delay);
}

// ========================================================================
// FUNCTION: HELLO
// ========================================================================

PHP_FUNCTION(shlomo_hello) {
    RETURN_STRING("Welcome to Epaphrodites in C!\n");
}

// ========================================================================
// MODULE INITIALIZATION
// ========================================================================

PHP_MINIT_FUNCTION(shlomo)
{
    // Enregistrement du type de ressource pour les promesses
    le_shlomo_promise = zend_register_list_destructors_ex(
        shlomo_promise_dtor, NULL, "ShlomoPromise", module_number);
    
    return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(shlomo)
{
    return SUCCESS;
}

PHP_MINFO_FUNCTION(shlomo)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "shlomo support", "enabled");
    php_info_print_table_row(2, "Version", PHP_SHLOMO_VERSION);
    php_info_print_table_end();
}

// ========================================================================
// FUNCTION MAPPING
// ========================================================================
static const zend_function_entry shlomo_functions[] = {
    PHP_FE(shlomo_hello, arginfo_shlomo_hello)
    PHP_FE(shlomo_async, arginfo_shlomo_async)
    PHP_FE(shlomo_await, arginfo_shlomo_await)
    PHP_FE(shlomo_then, arginfo_shlomo_then)
    PHP_FE(shlomo_task_info, arginfo_shlomo_task_info)
    PHP_FE_END
};

// ========================================================================
// MODULE ENTRY
// ========================================================================
zend_module_entry shlomo_module_entry = {
    STANDARD_MODULE_HEADER,
    "shlomo", // Extension name
    shlomo_functions, // Functions
    PHP_MINIT(shlomo),    // Module initialization
    PHP_MSHUTDOWN(shlomo), // Module shutdown
    NULL,                 // Request initialization
    NULL,                 // Request shutdown
    PHP_MINFO(shlomo),    // Module info
    PHP_SHLOMO_VERSION,
    STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(shlomo)