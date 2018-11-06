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
        $this->lockObject('lockeddata1');
        $this->expectException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage = $this->getInstance();
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
        $this->lockObject('lockeddata2');
        $this->expectException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage = $this->getInstance();
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
        $objectStorage = $this->getInstance();
        $objectStorage->set(
                [
                    'key' => 'lockeddata3',
                    'body' => 'data'
                ]
        );
        $this->lockObject('lockeddata3');
        $this->expectException('\IvoPetkov\ObjectStorage\ObjectLockedException');
        $objectStorage->delete(
                [
                    'key' => 'lockeddata3',
                ]
        );
    }

}
