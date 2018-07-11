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
class LocksTest extends ObjectStorageTestCase
{

    /**
     * 
     */
    public function testLockedKeys1()
    {
        $this->removeDataDir();
        $this->lockFile('lockeddata1');
        $this->setExpectedException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage = new \IvoPetkov\ObjectStorage($this->getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockeddata1',
                    'body' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testLockedKeys2()
    {
        $this->removeDataDir();
        $this->lockFile('lockeddata2');
        $this->setExpectedException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage = new \IvoPetkov\ObjectStorage($this->getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockeddata2',
                    'metadata.data1' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testLockedKeys3()
    {
        $this->removeDataDir();
        $objectStorage = new \IvoPetkov\ObjectStorage($this->getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockeddata3',
                    'body' => 'data'
                ]
        );
        $this->lockFile('lockeddata3');
        $this->setExpectedException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage->delete(
                [
                    'key' => 'lockeddata3',
                ]
        );
    }

}
