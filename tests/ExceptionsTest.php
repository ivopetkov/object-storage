<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) 2016 Ivo Petkov
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
        $this->removeDataDir();
        $dataDir = $this->getDataDir();
        $this->createFile($dataDir . '/objects', 'data');
        $this->setExpectedException('\IvoPetkov\ObjectStorage\ErrorException');
        $objectStorage = new \IvoPetkov\ObjectStorage($dataDir);
        $objectStorage->set(
                [
                    'key' => 'data1',
                    'body' => 'data'
                ]
        );
    }

}
