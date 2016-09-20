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
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new ObjectStorage($this->getDataDir());
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
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new ObjectStorage($this->getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockeddata2',
                    'metadata.data1' => 'data'
                ]
        );
    }

}
