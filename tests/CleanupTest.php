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
class CleanupTest extends ObjectStorageTestCase
{

    /**
     * 
     */
    public function testCleanup1()
    {
        $dataDir = $this->getDataDir();
        $objectStorage = new \IvoPetkov\ObjectStorage($dataDir . 'objects/', $dataDir . 'metadata/');

        $objectStorage->set(
            [
                'key' => 'services/category1/service1',
                'body' => 'service 1',
                'metadata.title' => 'Service 1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'books/category1/book1',
                'body' => 'book 1',
                'metadata.title' => 'Book 1'
            ]
        );
        $objectStorage->delete([
            'key' => 'books/category1/book1'
        ]);
        $objectStorage->set(
            [
                'key' => '.temp/user1',
                'body' => 'user 1',
                'metadata.title' => 'User 1'
            ]
        );
        $objectStorage->delete([
            'key' => '.temp/user1'
        ]);

        $this->assertEquals(scandir($dataDir . 'objects'), array(
            0 => '.',
            1 => '..',
            2 => '.temp',
            3 => 'books',
            4 => 'services',
        ));
        $this->assertEquals(scandir($dataDir . 'metadata/'), array(
            0 => '.',
            1 => '..',
            2 => '.temp',
            3 => 'books',
            4 => 'services',
        ));

        IvoPetkov\ObjectStorage\Utilities::cleanup($dataDir . 'objects');
        IvoPetkov\ObjectStorage\Utilities::cleanup($dataDir . 'metadata/');
        IvoPetkov\ObjectStorage\Utilities::cleanup($dataDir . 'missing');

        $this->assertEquals(scandir($dataDir . 'objects'), array(
            0 => '.',
            1 => '..',
            2 => 'services',
        ));
        $this->assertEquals(scandir($dataDir . 'metadata/'), array(
            0 => '.',
            1 => '..',
            2 => 'services',
        ));
    }
}
