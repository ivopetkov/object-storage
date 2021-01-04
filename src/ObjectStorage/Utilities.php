<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov\ObjectStorage;

/**
 * 
 */
class Utilities
{

    /**
     * Removes empty dirs in the dir specified.
     * 
     * @param string $dir The directory to cleanup.
     * @return void
     */
    static function cleanup(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $dir = rtrim($dir, '\\/');
        $removeEmptyDirs = function ($dir) use (&$removeEmptyDirs) {
            $subDirs = glob($dir . '/*', GLOB_ONLYDIR);
            $allSubDirsAreRemoved = true;
            foreach ($subDirs as $subDir) {
                if (!$removeEmptyDirs($subDir)) {
                    $allSubDirsAreRemoved = false;
                }
            }
            if ($allSubDirsAreRemoved) {
                if ($handle = opendir($dir)) {
                    $isEmpty = true;
                    while (($file = readdir($handle)) !== false) {
                        if ($file !== '.' && $file !== '..') {
                            $isEmpty = false;
                            break;
                        }
                    }
                    closedir($handle);
                    if ($isEmpty) {
                        rmdir($dir);
                        return true;
                    }
                }
            }
            return false;
        };
        $removeEmptyDirs($dir);
    }
}
