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
class DuplicatePerformanceTest extends ObjectStorageTestCase
{

    /**
     * No source metadata and no target metadata
     * @return void
     */
    public function testDuplicatePerformance1()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $objectStorage->duplicate(
            [
                'sourceKey' => 'book/book1',
                'targetKey' => 'book/book2',
            ]
        );

        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            1 =>
            array(
                0 => 'is_readable',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Create file dir.',
            ),
            4 =>
            array(
                0 => 'clearstatcache',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            5 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            6 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            7 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Duplicate command.',
            ),
            8 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Duplicate command.',
            ),
            9 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            10 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
        ));

        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'book/book1',
                    'result' => ['key', 'body', 'metadata']
                ],
                [
                    'command' => 'get',
                    'key' => 'book/book2',
                    'result' => ['key', 'body', 'metadata']
                ]
            ]
        );
        $this->assertEquals($result, array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book1',
            ),
        ));
    }

    /**
     * The source has metadata and the target does not have metadata
     * @return void
     */
    public function testDuplicatePerformance2()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $objectStorage->duplicate(
            [
                'sourceKey' => 'book/book1',
                'targetKey' => 'book/book2',
            ]
        );

        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            1 =>
            array(
                0 => 'is_readable',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Create file dir.',
            ),
            4 =>
            array(
                0 => 'clearstatcache',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            5 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            6 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            7 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Duplicate command.',
            ),
            8 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            9 =>
            array(
                0 => 'is_readable',
                1 => 'METADATADIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            10 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            11 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book',
                2 => 'Create file dir.',
            ),
            12 =>
            array(
                0 => 'clearstatcache',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            13 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            14 =>
            array(
                0 => 'fopen',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            15 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            16 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            17 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Get file content.',
            ),
            18 =>
            array(
                0 => 'fopen',
                1 => 'METADATADIR/book/book1',
                2 => 'Get file content.',
            ),
        ));

        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'book/book1',
                    'result' => ['key', 'body', 'metadata']
                ],
                [
                    'command' => 'get',
                    'key' => 'book/book2',
                    'result' => ['key', 'body', 'metadata']
                ]
            ]
        );
        $this->assertEquals($result, array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ),
        ));
    }

    /**
     * The source has metadata and the target has metadata
     * @return void
     */
    public function testDuplicatePerformance3()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2',
                'metadata.metaA' => 'valueA'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $objectStorage->duplicate(
            [
                'sourceKey' => 'book/book1',
                'targetKey' => 'book/book2',
            ]
        );

        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            1 =>
            array(
                0 => 'is_readable',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Create file dir.',
            ),
            4 =>
            array(
                0 => 'clearstatcache',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            5 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            6 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            7 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Duplicate command.',
            ),
            8 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            9 =>
            array(
                0 => 'is_readable',
                1 => 'METADATADIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            10 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            11 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book',
                2 => 'Create file dir.',
            ),
            12 =>
            array(
                0 => 'clearstatcache',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            13 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            14 =>
            array(
                0 => 'fopen',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            15 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            16 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            17 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Get file content.',
            ),
            18 =>
            array(
                0 => 'fopen',
                1 => 'METADATADIR/book/book1',
                2 => 'Get file content.',
            ),
        ));

        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'book/book1',
                    'result' => ['key', 'body', 'metadata']
                ],
                [
                    'command' => 'get',
                    'key' => 'book/book2',
                    'result' => ['key', 'body', 'metadata']
                ]
            ]
        );
        $this->assertEquals($result, array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book1',
                'metadata.meta1' => 'value1',
                'metadata.meta2' => 'value2',
            ),
        ));
    }


    /**
     * No source metadata and the target has metadata
     * @return void
     */
    public function testDuplicatePerformance4()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2',
                'metadata.metaA' => 'valueA'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $objectStorage->duplicate(
            [
                'sourceKey' => 'book/book1',
                'targetKey' => 'book/book2',
            ]
        );

        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            1 =>
            array(
                0 => 'is_readable',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Prepare for reading.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Create file dir.',
            ),
            4 =>
            array(
                0 => 'clearstatcache',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            5 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            6 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            7 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book1',
                2 => 'Duplicate command.',
            ),
            8 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Duplicate command.',
            ),
            9 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            10 =>
            array(
                0 => 'is_dir',
                1 => 'METADATADIR/book',
                2 => 'Create file dir.',
            ),
            11 =>
            array(
                0 => 'clearstatcache',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            12 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            13 =>
            array(
                0 => 'fopen',
                1 => 'METADATADIR/book/book2',
                2 => 'Prepare for writing.',
            ),
            14 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            15 =>
            array(
                0 => 'fopen',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get file content.',
            ),
            16 =>
            array(
                0 => 'is_file',
                1 => 'METADATADIR/book/book2',
                2 => 'Remove deleted files.',
            ),
            17 =>
            array(
                0 => 'unlink',
                1 => 'METADATADIR/book/book2',
                2 => 'Remove deleted files.',
            ),
        ));

        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'book/book1',
                    'result' => ['key', 'body', 'metadata']
                ],
                [
                    'command' => 'get',
                    'key' => 'book/book2',
                    'result' => ['key', 'body', 'metadata']
                ]
            ]
        );
        $this->assertEquals($result, array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book1',
            ),
        ));
    }
}
