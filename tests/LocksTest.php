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
        removeDir('data/');
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new TestObjectStorage('data/');
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
        removeDir('data/');
    }

    /**
     * @runInSeparateProcess
     */
    public function testLockedKeys2()
    {
        removeDir('data/');
        $this->setExpectedException('ObjectStorage\ObjectLockedException');
        $objectStorage = new TestObjectStorage('data/');
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
        removeDir('data/');
    }

}
