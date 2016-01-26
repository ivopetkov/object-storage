<?php

class KeyCollisionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testFileWhenThereIsDirWithTheSameKey()
    {
        removeDataDir();

        $objectStorage = getInstance();

        $result = $objectStorage->set(
                [
                    'key' => 'data1/data2',
                    'body' => 'data'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('46fe6220dc64b1fdac801a01f30f9296
objects/data1/data2: data
'));

        $this->setExpectedException('\ObjectStorage\ErrorException');
        $result = $objectStorage->set(
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
        removeDataDir();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDirWhenThereIsFileWithTheSameKey()
    {
        removeDataDir();

        $objectStorage = getInstance();

        $result = $objectStorage->set(
                [
                    'key' => 'data1',
                    'body' => 'data'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('eaa438b85d60666c5b30b3ed8f4affc6
objects/data1: data
'));

        $this->setExpectedException('\ObjectStorage\ErrorException');
        $result = $objectStorage->set(
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
        removeDataDir();
    }

}
