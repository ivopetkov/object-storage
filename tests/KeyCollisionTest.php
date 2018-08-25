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

        $this->expectException('\IvoPetkov\ObjectStorage\ErrorException');
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

        $this->expectException('\IvoPetkov\ObjectStorage\ErrorException');
        $objectStorage->set(
                [
                    'key' => 'data1/data2',
                    'body' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testGetKeyWhenItsDir()
    {
        $this->removeDataDir();

        $objectStorage = $this->getInstance();

        $this->createFile($this->getDataDir() . '/objects/data1/key1', 'content');
        $result = $objectStorage->get([
            'key' => 'data1',
            'result' => ['key', 'body']
        ]);

        $this->assertTrue($result === null);

        $this->assertTrue($this->checkState('3fb020a8835e135fb296ad20b43ba773
objects/data1/key1: content
'));
    }

    /**
     * 
     */
    public function testDeleteKeyWhenItsDir()
    {
        $this->removeDataDir();

        $objectStorage = $this->getInstance();

        $this->createFile($this->getDataDir() . '/objects/data1/key1', 'content');

        $objectStorage->delete([
            'key' => 'data1'
        ]);

        $this->assertTrue($this->checkState('3fb020a8835e135fb296ad20b43ba773
objects/data1/key1: content
'));
    }

}
