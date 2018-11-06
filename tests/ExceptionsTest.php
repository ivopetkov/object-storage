<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class ExceptionsTest extends ObjectStorageTestCase
{

    /**
     * 
     */
    public function testExceptions1()
    {
        $dataDir = $this->getDataDir();
        $this->createFile($dataDir . '/objects', 'data');
        $this->expectException('\IvoPetkov\ObjectStorage\ErrorException');
        $objectStorage = $this->getInstance();
        $objectStorage->set(
                [
                    'key' => 'data1',
                    'body' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testExceptions2()
    {
        $this->expectException('\IvoPetkov\ObjectStorage\ObjectNotFoundException');
        $objectStorage = $this->getInstance();
        $objectStorage->rename(
                [
                    'sourceKey' => 'key1',
                    'targetKey' => 'key2'
                ]
        );
    }

}
