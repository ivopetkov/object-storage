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
class SearchPerformanceTest extends ObjectStorageTestCase
{

    /**
     * 
     * @return void
     */
    public function testSearchPerformance1()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
            ),
            2 =>
            array(
                'key' => 'book/object3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get files list.',
            ),
            5 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Get files list.',
            ),
            6 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance2()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/book1', 'equal'],
                    ['key', 'book/book5', 'equal'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance3()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/book1', 'equal'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance4()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book', 'equal'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance5()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book2/', 'startWith'],
                    ['key', 'book/', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance6()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/', 'startWith'],
                    ['key', 'book/b', 'startWith'],
                    ['key', 'book/bo', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
            ),
            2 =>
            array(
                'key' => 'book/object3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book1',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance7()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/', 'startWith'],
                    ['key', 'book/b', 'notStartWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance8()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/b', 'notStartWith'],
                    ['key', 'boo', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
            )
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
                2 => 'Get files list.',
            )
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance9()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'book/book1',
                'body' => 'book1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/book2',
                'body' => 'book2'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book/object3',
                'body' => 'book3'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/b', 'notStartWith'],
                    ['key', 'book/c', 'notStartWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance10()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/books/2',
            ),
            1 =>
            array(
                'key' => 'company/products/computers/1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/books/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books/2',
                2 => 'Get files list.',
            ),
            5 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
                2 => 'Get files list.',
            ),
            6 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
                2 => 'Get files list.',
            ),
            7 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance11()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/books/2',
            ),
            1 =>
            array(
                'key' => 'company/products/computers/1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books',
                2 => 'Get files list.',
            ),
            5 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/books/',
                2 => 'Get files list.',
            ),
            6 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books/2',
                2 => 'Get files list.',
            ),
            7 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
                2 => 'Get files list.',
            ),
            8 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
                2 => 'Get files list.',
            ),
            9 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance12()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '/', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance13()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/com', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/computers/1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/',
                2 => 'Get files list.',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
                2 => 'Get files list.',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
                2 => 'Get files list.',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
                2 => 'Get files list.',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
                2 => 'Get files list.',
            ),
        ));
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance14()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers', 'notStartWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance15()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/books', 'startWith'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance16()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers/1', 'notEqual'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array());
    }

    /**
     * 
     * @return void
     */
    public function testSearchPerformance17()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->set(
            [
                'key' => 'company/products/computers/1',
                'body' => 'computer1'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'company/products/books/2',
                'body' => 'book2'
            ]
        );
        $objectStorage->internalStorageAccessLog = [];
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers', 'notEqual'],
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/computers/1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/company/products/computers/1',
                2 => 'Get files list.',
            ),
        ));
    }
}
