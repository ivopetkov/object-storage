<?php

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

}
