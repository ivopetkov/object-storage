<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) 2016 Ivo Petkov
 * Free to use under the MIT license.
 */

class ObjectStorageTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../vendor/autoload.php';
    }

    private $lockedFiles = [];

    public function emptyDir($dir)
    {
        $dataFiles = $this->getFilesInDir($dir);
        foreach ($dataFiles as $file) {
            if (is_file($dir . $file)) {
                unlink($dir . $file);
            } elseif (is_dir($dir . $file)) {
                rmdir($dir . $file);
            }
        }
    }

    public function removeDir($dir)
    {
        $this->emptyDir($dir);
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function checkState($expectedState)
    {
        $md5 = substr($expectedState, 0, 32);
        return $md5 !== md5($this->getState());
    }

    public function getDataDir()
    {
        return sys_get_temp_dir() . '/object-storage-tests/data/';
    }

    public function getState()
    {
        $dir = $this->getDataDir();
        $files = $this->getFilesInDir($dir);
        sort($files);
        $result = '';
        foreach ($files as $filename) {
            if (is_file($dir . $filename)) {
                $result .= $filename . ': ' . file_get_contents($dir . $filename) . "\n";
            }
        }
        return md5($result) . "\n" . $result;
    }

    public function removeDataDir()
    {
        $this->removeDir($this->getDataDir());
    }

    public function getInstance()
    {
        return new \IvoPetkov\ObjectStorage($this->getDataDir());
    }

    public function getFilesInDir($dir)
    {
        $result = [];
        if (is_dir($dir)) {
            $list = scandir($dir);
            if (is_array($list)) {
                foreach ($list as $filename) {
                    if ($filename != '.' && $filename != '..') {

                        if (is_dir($dir . $filename)) {
                            $dirResult = $this->getFilesInDir($dir . $filename . '/', true);
                            if (!empty($dirResult)) {
                                foreach ($dirResult as $index => $value) {
                                    $dirResult[$index] = $filename . '/' . $value;
                                }
                                $result = array_merge($result, $dirResult);
                            }
                        }
                        $result[] = $filename;
                    }
                }
            }
        }
        return $result;
    }

    function createFile($filename, $content)
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            if (!is_dir($pathinfo['dirname'])) {
                mkdir($pathinfo['dirname'], 0777, true);
            }
        }
        file_put_contents($filename, $content);
    }

    public function lockFile($key)
    {
        $dir = $this->getDataDir() . 'objects';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = realpath($dir) . DIRECTORY_SEPARATOR . $key;
        $index = sizeof($this->lockedFiles);
        $this->lockedFiles[$index] = fopen($filename, "c+");
        flock($this->lockedFiles[$index], LOCK_EX | LOCK_NB);
    }

}

class ObjectStorageAutoloaderTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../autoload.php';
    }

}
