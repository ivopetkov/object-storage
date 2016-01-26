<?php

class TestObjectStorage extends ObjectStorage
{

    function getFilePointerForWriting($filename)
    {
        $filePointer = fopen($filename, "c+");
        flock($filePointer, LOCK_EX | LOCK_NB);
        return parent::getFilePointerForWriting($filename);
    }

}

class LocksTest extends PHPUnit_Framework_TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testLockedKeys1()
    {
        removeDataDir();
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new TestObjectStorage(getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockedata',
                    'body' => 'data'
                ]
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLockedKeys1CleanUp()
    {
        removeDataDir();
    }

    /**
     * @runInSeparateProcess
     */
    public function testLockedKeys2()
    {
        removeDataDir();
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new TestObjectStorage(getDataDir());
        $objectStorage->set(
                [
                    'key' => 'lockedata',
                    'metadata.data1' => 'data'
                ]
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLockedKeys2CleanUp()
    {
        removeDataDir();
    }

}
