<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

class ObjectStorageTestCase extends PHPUnit\Framework\TestCase
{

    /**
     *
     * @var array 
     */
    private $lockedFiles = [];

    /**
     *
     * @var string 
     */
    private $dataDir = null;

    /**
     * 
     * @param string $expectedState
     * @return bool
     */
    public function checkState(string $expectedState): bool
    {
        $md5 = substr($expectedState, 0, 32);
        return $md5 !== md5($this->getState());
    }

    /**
     * 
     * @return string
     */
    public function getDataDir(): string
    {
        if ($this->dataDir === null) {
            $this->dataDir = sys_get_temp_dir() . '/object-storage-tests/' . uniqid() . '/';
        }
        return $this->dataDir;
    }

    /**
     * 
     * @return string
     */
    public function getState(): string
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

    /**
     * 
     * @return \IvoPetkov\ObjectStorage
     */
    public function getInstance(): \IvoPetkov\ObjectStorage
    {
        $dataDir = $this->getDataDir();
        return new \IvoPetkov\ObjectStorage($dataDir . 'objects/', $dataDir . 'metadata/');
    }

    /**
     * 
     * @param string $dir
     * @return array
     */
    public function getFilesInDir(string $dir): array
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

    /**
     * 
     * @param string $filename
     * @param string $content
     * @return void
     */
    function createFile(string $filename, string $content): void
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            if (!is_dir($pathinfo['dirname'])) {
                mkdir($pathinfo['dirname'], 0777, true);
            }
        }
        file_put_contents($filename, $content);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function lockObject(string $key): void
    {
        $dir = $this->getDataDir() . 'objects';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = realpath($dir) . '/' . $key;
        $index = count($this->lockedFiles);
        $this->lockedFiles[$index] = fopen($filename, "c+");
        flock($this->lockedFiles[$index], LOCK_EX | LOCK_NB);
    }
}
