<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov\ObjectStorage;

class ObjectLockedException extends \Exception
{

    /**
     * Construct the exception
     * 
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining.
     * </p>
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
