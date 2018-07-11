<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

$classes = array(
    'IvoPetkov\ObjectStorage' => 'src/ObjectStorage.php',
    'IvoPetkov\ObjectStorage\ErrorException' => 'src/ObjectStorage/ErrorException.php',
    'IvoPetkov\ObjectStorage\ObjectLockedException' => 'src/ObjectStorage/ObjectLockedException.php',
);

spl_autoload_register(function ($class) use ($classes) {
    if (isset($classes[$class])) {
        require __DIR__ . '/' . $classes[$class];
    }
}, true);
