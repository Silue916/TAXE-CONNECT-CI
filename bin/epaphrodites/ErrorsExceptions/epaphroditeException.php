<?php

namespace Epaphrodites\epaphrodites\ErrorsExceptions;

final class epaphroditeException extends \Exception
{

    // Constructor for MyCustomException class
    public function __construct(
        string $msg = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        // Call the parent constructor with provided message, code, and previous exception
        parent::__construct($msg, $code, $previous);
    }

    /**
     * Custom function specific to epaphroditeException
     * @return string
     */
    public function epaphroditeException(): string
    {
        return "This is an epaphroditeException function for handling Exceptions!";
    }
}
