<?php

spl_autoload_register(function ($class) {
    if ($class === 'ObjectStorage') {
        require __DIR__ . '/ObjectStorage.php';
    } elseif ($class === 'ObjectStorage\ErrorException') {
        require __DIR__ . '/ObjectStorageErrorException.php';
    } elseif ($class === 'ObjectStorage\ObjectLockedException') {
        require __DIR__ . '/ObjectStorageObjectLockedException.php';
    }
}, true);
