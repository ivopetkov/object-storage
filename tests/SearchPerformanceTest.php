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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book2',
            ),
            2 =>
            array(
                'key' => 'book/object3',
                'body' => 'book3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book1',
            ),
            5 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
            ),
            6 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/book1', 'equal'],
                    ['key', 'book/book5', 'equal'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/book1', 'equal'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book/book1',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book', 'equal'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array());
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/book',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book2/', 'startWith'],
                    ['key', 'book/', 'startWith'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/', 'startWith'],
                    ['key', 'book/b', 'startWith'],
                    ['key', 'book/bo', 'startWith'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1',
                'body' => 'book1',
            ),
            1 =>
            array(
                'key' => 'book/book2',
                'body' => 'book2',
            ),
            2 =>
            array(
                'key' => 'book/object3',
                'body' => 'book3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book1',
            ),
            3 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/book2',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/', 'startWith'],
                    ['key', 'book/b', 'notStartWith'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
                'body' => 'book3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/b', 'notStartWith'],
                    ['key', 'boo', 'startWith'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
                'body' => 'book3',
            )
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
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
        $objectStorage->internalStorageAccessLog = [];
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/b', 'notStartWith'],
                    ['key', 'book/c', 'notStartWith'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/object3',
                'body' => 'book3',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/book/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/book/object3',
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
        $objectStorage->internalStorageAccessLog = [];
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
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/books/2',
                'body' => 'book2',
            ),
            1 =>
            array(
                'key' => 'company/products/computers/1',
                'body' => 'computer1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/books/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books/2',
            ),
            5 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
            ),
            6 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
            ),
            7 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
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
        $objectStorage->internalStorageAccessLog = [];
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
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/books/2',
                'body' => 'book2',
            ),
            1 =>
            array(
                'key' => 'company/products/computers/1',
                'body' => 'computer1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books',
            ),
            5 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/books/',
            ),
            6 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/books/2',
            ),
            7 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
            ),
            8 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
            ),
            9 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
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
        $objectStorage->internalStorageAccessLog = [];
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
                    ['key', '/', 'startWith'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/computers/1',
                'body' => 'computer1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/',
            ),
            1 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/',
            ),
            2 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers',
            ),
            3 =>
            array(
                0 => 'scandir',
                1 => 'OBJECTSDIR/company/products/computers/',
            ),
            4 =>
            array(
                0 => 'is_dir',
                1 => 'OBJECTSDIR/company/products/computers/1',
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
        $objectStorage->internalStorageAccessLog = [];
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
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers', 'notStartWith'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/books', 'startWith'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers/1', 'notEqual'],
                ],
                'result' => ['key', 'body']
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
        $objectStorage->internalStorageAccessLog = [];
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
                    ['key', 'company/products/computers/1', 'equal'],
                    ['key', 'company/products/computers', 'notEqual'],
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'company/products/computers/1',
                'body' => 'computer1',
            ),
        ));
        $this->assertTrue($objectStorage->internalStorageAccessLog === array(
            0 =>
            array(
                0 => 'is_file',
                1 => 'OBJECTSDIR/company/products/computers/1',
            ),
        ));
    }
}
