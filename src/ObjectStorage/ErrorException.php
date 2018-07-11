<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov\ObjectStorage;

class ErrorException extends \ErrorException
{

    /**
     * Constructs the exception
     * @param string $message [optional] <p>
     * The Exception message to throw.
     * </p>
     * @param int $code [optional] <p>
     * The Exception code.
     * </p>
     * @param int $severity [optional] <p>
     * The severity level of the exception.
     * </p>
     * @param string $filename [optional] <p>
     * The filename where the exception is thrown.
     * </p>
     * @param int $lineno [optional] <p>
     * The line number where the exception is thrown.
     * </p>
     * @param Exception $previous [optional] <p>
     * The previous exception used for the exception chaining.
     * </p>
     */
    public function __construct($message = "", $code = 0, $severity = E_ERROR, $filename = __FILE__, $lineno = __LINE__, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }

}
