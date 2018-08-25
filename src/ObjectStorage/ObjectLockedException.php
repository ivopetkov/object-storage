<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov\ObjectStorage;

/**
 * Exception that will be thrown when trying to modify an object that is used by other process.
 */
class ObjectLockedException extends \IvoPetkov\ObjectStorage\ErrorException
{
    
}
