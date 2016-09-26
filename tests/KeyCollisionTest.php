<?php

/**
 * @runTestsInSeparateProcesses
 */
class KeyCollisionTest extends ObjectStorageTestCase
{

    /**
     *
     */
    public function testFileWhenThereIsDirWithTheSameKey()
    {
        $this->removeDataDir();

        $objectStorage = $this->getInstance();

        $objectStorage->set(
                [
                    'key' => 'data1/data2',
                    'body' => 'data'
                ]
        );
        $this->assertTrue($this->checkState('46fe6220dc64b1fdac801a01f30f9296
objects/data1/data2: data
'));

        $this->setExpectedException('\IvoPetkov\ObjectStorage\ErrorException');
        $objectStorage->set(
                [
                    'key' => 'data1',
                    'body' => 'data'
                ]
        );

        //echo "\n\n" . var_export($result) . "\n\n" . getState() . "\n\n";
    }

    /**
     * 
     */
    public function testFileWhenThereIsDirWithTheSameKeyCleanUp()
    {
        $this->removeDataDir();
    }

    /**
     * 
     */
    public function testDirWhenThereIsFileWithTheSameKey()
    {
        $this->removeDataDir();

        $objectStorage = $this->getInstance();

        $objectStorage->set(
                [
                    'key' => 'data1',
                    'body' => 'data'
                ]
        );
        $this->assertTrue($this->checkState('eaa438b85d60666c5b30b3ed8f4affc6
objects/data1: data
'));

        $this->setExpectedException('\IvoPetkov\ObjectStorage\ErrorException');
        $objectStorage->set(
                [
                    'key' => 'data1/data2',
                    'body' => 'data'
                ]
        );

        //echo "\n\n" . var_export($result) . "\n\n" . getState() . "\n\n";
    }

    /**
     * 
     */
    public function testDirWhenThereIsFileWithTheSameKeyCleanUp()
    {
        $this->removeDataDir();
    }

}
