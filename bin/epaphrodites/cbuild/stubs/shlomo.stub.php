<?php

/**
 * Shlomo Extension - PHP stub file
 * 
 * This file contains function signatures for the shlomo extension.
 * It is meant to provide IDE autocompletion and documentation.
 */

/**
 * Displays a welcome message for the Epaphrodites framework.
 * 
 * @return string A welcome message string
 */
function shlomo_hello(): string {}

/**
 * Displays a welcome message for the Epaphrodites framework.
 * 
 * @return string A welcome message string
 */
function hello_noella(): string {}

/**
 * Executes a PHP function asynchronously in a separate thread.
 * 
 * @param callable $func The function to execute asynchronously
 * @return resource A promise resource that can be used with shlomo_await or shlomo_then
 * @throws Exception If the function is not callable or thread creation fails
 */
function shlomo_async(callable $func) {}

/**
 * Waits for an asynchronous operation to complete and returns its result.
 * 
 * @param resource $promise The promise resource returned by shlomo_async
 * @return mixed The result of the asynchronous function
 * @throws Exception If the provided resource is not a valid promise
 */
function shlomo_await($promise) {}

/**
 * Registers a callback function to be executed when the promise is fulfilled.
 * 
 * @param resource $promise The promise resource returned by shlomo_async
 * @param callable $callback The function to call when the promise completes
 * @return bool True on success, false on failure
 * @throws Exception If the provided resource is not a valid promise or callback is not callable
 */
function shlomo_then($promise, callable $callback): bool {}

/**
 * Outputs JavaScript code to be executed after a specified delay.
 * 
 * @param string $js_code JavaScript code to execute in the browser
 * @param int $delay Time to wait before execution (in milliseconds)
 * @return void
 */
function shlomo_task_info(string $js_code, int $delay): void {}