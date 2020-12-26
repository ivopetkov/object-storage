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
class DataTest extends ObjectStorageTestCase
{

    /**
     *
     */
    public function testDataState1()
    {
        $objectStorage = $this->getInstance();

        // Create initial data
        $objectStorage->set(
            [
                'key' => 'book-1449392776',
                'body' => 'book 1449392776 content in pdf format',
                'metadata.title' => 'Programming PHP',
                'metadata.authors' => '["Kevin Tatroe", "Peter MacIntyre", "Rasmus Lerdorf"]',
                'metadata.year' => '2013'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book-1430260319',
                'body' => 'book 1430260319 content in pdf format',
                'metadata.title' => 'PHP Objects, Patterns, and Practice',
                'metadata.authors' => '["Matt Zandstra"]',
                'metadata.year' => '2013'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book-1430268158',
                'body' => 'book 1430268158 content in pdf format',
                'metadata.title' => 'PHP for Absolute Beginners',
                'metadata.authors' => '["Jason Lengstorf", "Thomas Blom Hansen"]',
                'metadata.year' => '2014'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book-1000000000'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'book-2000000000',
                'body' => 'book 2000000000 content in pdf format'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'books/3000000000',
                'body' => 'book 3000000000 content in pdf format',
                'metadata.year' => '2014'
            ]
        );
        $this->assertTrue($this->checkState('ed20ab8c4a519ca9fc10608331cd8bc1
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'));



        // Search
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book-1449392776']
                    //['key', ['book-1449392776', 'book-1430268158']]
                ],
                'result' => ['key', 'body', 'metadata.title', 'metadata.year']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book-1449392776',
                'body' => 'book 1449392776 content in pdf format',
                'metadata.title' => 'Programming PHP',
                'metadata.year' => '2013',
            ),
        ));
        $this->assertTrue($this->checkState(
            'ed20ab8c4a519ca9fc10608331cd8bc1
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));



        // Set (update metadata)
        $objectStorage->set(
            [
                'key' => 'book-1449392776',
                'metadata.rating' => '3.4'
            ]
        );
        $this->assertTrue($this->checkState(
            '534393dff9b252fcc411e1c2b8e501ed
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));



        // Get
        $result = $objectStorage->get(
            [
                'key' => 'book-1449392776',
                'result' => []
            ]
        );
        $this->assertTrue($result === []);
        $result = $objectStorage->get(
            [
                'key' => 'book-1449392776',
                'result' => ['metadata.rating']
            ]
        );
        $this->assertTrue($result === array(
            'metadata.rating' => '3.4',
        ));
        $this->assertTrue($this->checkState(
            '534393dff9b252fcc411e1c2b8e501ed
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));

        // Exists
        $result = $objectStorage->exists(
            [
                'key' => 'book-1449392776'
            ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->exists(
            [
                'key' => 'book-1449392776-missing'
            ]
        );
        $this->assertTrue($result === false);
        $this->assertTrue($this->checkState(
            '534393dff9b252fcc411e1c2b8e501ed
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));


        // Set
        $objectStorage->set(
            [
                'key' => 'book-1449392776-comments',
                'body' => "John Smith: This book is awaseome.\n"
            ]
        );
        $this->assertTrue($this->checkState(
            '0b1f6ab2f7435f15566e50a89a6173ff
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-1449392776-comments: John Smith: This book is awaseome.

objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));



        // Append
        $objectStorage->append(
            [
                'key' => 'book-1449392776-comments',
                'body' => "Oliver Mark: Best book I've ever read.\n"
            ]
        );
        $this->assertTrue($this->checkState(
            '5acced67220df7bf1d104773c67555eb
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-1449392776-comments: John Smith: This book is awaseome.
Oliver Mark: Best book I\'ve ever read.

objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));



        // delete
        $objectStorage->delete(
            [
                'key' => 'book-1449392776-comments'
            ]
        );
        $this->assertTrue($this->checkState(
            '534393dff9b252fcc411e1c2b8e501ed
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));



        // append
        $objectStorage->append(
            [
                'key' => 'book-1000000000-comments',
                'body' => "Ivo Petkov: Great!.\n",
                'metadata.ivo' => "test"
            ]
        );
        $this->assertTrue($this->checkState(
            '65c9e2014d2e54752d99a49f56c3fe1e
metadata/book-1000000000-comments: content-type:json

{"ivo":"test"}
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1000000000-comments: Ivo Petkov: Great!.

objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'
        ));


        // search
        $result = $objectStorage->search(
            [
                'where' => [
                    ['metadata.year', '2013']
                ],
                'result' => ['key', 'metadata.title', 'metadata.authors']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book-1430260319',
                'metadata.title' => 'PHP Objects, Patterns, and Practice',
                'metadata.authors' => '["Matt Zandstra"]',
            ),
            1 =>
            array(
                'key' => 'book-1449392776',
                'metadata.title' => 'Programming PHP',
                'metadata.authors' => '["Kevin Tatroe", "Peter MacIntyre", "Rasmus Lerdorf"]',
            ),
        ));
        $this->assertTrue($this->checkState('65c9e2014d2e54752d99a49f56c3fe1e
metadata/book-1000000000-comments: content-type:json

{"ivo":"test"}
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1000000000-comments: Ivo Petkov: Great!.

objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'));





        // get
        $result = $objectStorage->get(
            [
                'key' => 'book-1449392776',
                'result' => ['key', 'body', 'metadata']
            ]
        );
        $this->assertTrue($result === array(
            'key' => 'book-1449392776',
            'body' => 'book 1449392776 content in pdf format',
            'metadata.title' => 'Programming PHP',
            'metadata.authors' => '["Kevin Tatroe", "Peter MacIntyre", "Rasmus Lerdorf"]',
            'metadata.year' => '2013',
            'metadata.rating' => '3.4',
        ));
        $this->assertTrue($this->checkState('65c9e2014d2e54752d99a49f56c3fe1e
metadata/book-1000000000-comments: content-type:json

{"ivo":"test"}
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1000000000-comments: Ivo Petkov: Great!.

objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'));


        // duplicate
        $objectStorage->duplicate(
            [
                'sourceKey' => 'book-1449392776',
                'targetKey' => 'book-1449392776-copy'
            ]
        );
        $this->assertTrue($this->checkState('9b0ef8154be7ef0b7b939f7ca2e9af77
metadata/book-1000000000-comments: content-type:json

{"ivo":"test"}
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/book-1449392776-copy: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1000000000-comments: Ivo Petkov: Great!.

objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776: book 1449392776 content in pdf format
objects/book-1449392776-copy: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'));



        // rename
        $objectStorage->rename(
            [
                'sourceKey' => 'book-1449392776',
                'targetKey' => 'book-5000000000'
            ]
        );
        $this->assertTrue($this->checkState('8171d7214894ef37665cd74576bde01f
metadata/book-1000000000-comments: content-type:json

{"ivo":"test"}
metadata/book-1430260319: content-type:json

{"title":"PHP Objects, Patterns, and Practice","authors":"[\\"Matt Zandstra\\"]","year":"2013"}
metadata/book-1430268158: content-type:json

{"title":"PHP for Absolute Beginners","authors":"[\\"Jason Lengstorf\\", \\"Thomas Blom Hansen\\"]","year":"2014"}
metadata/book-1449392776-copy: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/book-5000000000: content-type:json

{"title":"Programming PHP","authors":"[\\"Kevin Tatroe\\", \\"Peter MacIntyre\\", \\"Rasmus Lerdorf\\"]","year":"2013","rating":"3.4"}
metadata/books/3000000000: content-type:json

{"year":"2014"}
objects/book-1000000000: 
objects/book-1000000000-comments: Ivo Petkov: Great!.

objects/book-1430260319: book 1430260319 content in pdf format
objects/book-1430268158: book 1430268158 content in pdf format
objects/book-1449392776-copy: book 1449392776 content in pdf format
objects/book-2000000000: book 2000000000 content in pdf format
objects/book-5000000000: book 1449392776 content in pdf format
objects/books/3000000000: book 3000000000 content in pdf format
'));



        // delete
        $objectStorage->delete(
            [
                'key' => 'book-1000000000'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-1000000000-comments'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-1449392776'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-1430260319'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-1430268158'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-2000000000'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'books/3000000000'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-1449392776-copy'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'book-5000000000'
            ]
        );
        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));


        // multiple commands
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-5',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'append',
                    'key' => 'products',
                    'body' => '[5]'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-6',
                    'body' => 'product body'
                ],
                [
                    'command' => 'append',
                    'key' => 'products',
                    'body' => '[6]'
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-6'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-5',
                    'result' => ['key', 'body']
                ],
                [
                    'command' => 'exists',
                    'key' => 'product-5'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-5',
                    'body' => 'product body 2',
                    'metadata.ivo' => '2011'
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-5']
                    ],
                    'result' => ['key', 'body', 'metadata']
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-5'
                ],
                [
                    'command' => 'exists',
                    'key' => 'product-5'
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-5']
                    ],
                    'result' => ['key', 'body']
                ],
                [
                    'command' => 'set',
                    'key' => 'product-5',
                    'body' => 'product body 3'
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-5']
                    ],
                    'result' => ['key', 'body']
                ],
                [
                    'command' => 'search',
                    'result' => ['key', 'body']
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-5'
                ],
                [
                    'command' => 'delete',
                    'key' => 'products'
                ]
            ]
        );

        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null,
            3 => null,
            4 => null,
            5 => array(
                'key' => 'product-5',
                'body' => 'product body 1',
            ),
            6 => true,
            7 => null,
            8 => array(
                0 => array(
                    'key' => 'product-5',
                    'body' => 'product body 2',
                    'metadata.ivo' => '2011',
                ),
            ),
            9 => null,
            10 => false,
            11 => array(),
            12 => null,
            13 => array(
                0 =>
                array(
                    'key' => 'product-5',
                    'body' => 'product body 3',
                ),
            ),
            14 => array(
                0 =>
                array(
                    'key' => 'product-5',
                    'body' => 'product body 3',
                ),
                1 =>
                array(
                    'key' => 'products',
                    'body' => '[5][6]',
                ),
            ),
            15 => null,
            16 => null,
        ));
        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));



        // test delete metadata
        $objectStorage->set(
            [
                'key' => 'test-delete-metadata',
                'metadata.rating' => '3.4'
            ]
        );
        $this->assertTrue($this->checkState('e6ff92ea7fb169ba3e87676f3bbc379e
metadata/test-delete-metadata: content-type:json

{"rating":"3.4"}
objects/test-delete-metadata: 
'));

        $objectStorage->set(
            [
                'key' => 'test-delete-metadata',
                'metadata.rating' => ''
            ]
        );
        $this->assertTrue($this->checkState('366ff7633ebcec69cf1954175e4aa7fc
objects/test-delete-metadata: '));

        $objectStorage->delete(
            [
                'key' => 'test-delete-metadata'
            ]
        );
        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));



        // regexp search
        $objectStorage->set(
            [
                'key' => 'prefix1/dataa',
                'body' => 'A'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'prefix1/datab',
                'body' => 'B'
            ]
        );
        $objectStorage->set(
            [
                'key' => 'prefix2/datac',
                'body' => 'C'
            ]
        );
        $this->assertTrue($this->checkState('f34e94bee39fd95da85fc91a3ca302ea
objects/prefix1/dataa: A
objects/prefix1/datab: B
objects/prefix2/datac: C
'));

        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '^prefix1\/', 'regExp']
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'prefix1/dataa',
                'body' => 'A',
            ),
            1 =>
            array(
                'key' => 'prefix1/datab',
                'body' => 'B',
            ),
        ));
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'prefix1/', 'startWith']
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'prefix1/dataa',
                'body' => 'A',
            ),
            1 =>
            array(
                'key' => 'prefix1/datab',
                'body' => 'B',
            ),
        ));
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'efix1/da', 'contain']
                ],
                'result' => ['key', 'body']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'prefix1/dataa',
                'body' => 'A',
            ),
            1 =>
            array(
                'key' => 'prefix1/datab',
                'body' => 'B',
            ),
        ));
        $this->assertTrue($this->checkState('f34e94bee39fd95da85fc91a3ca302ea
objects/prefix1/dataa: A
objects/prefix1/datab: B
objects/prefix2/datac: C
'));

        $objectStorage->delete(
            [
                'key' => 'prefix1/dataa'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'prefix1/datab'
            ]
        );
        $objectStorage->delete(
            [
                'key' => 'prefix2/datac'
            ]
        );
        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testEmptyData()
    {
        $objectStorage = $this->getInstance();

        $objectStorage->set(
            [
                'key' => 'emptydata',
                'body' => ''
            ]
        );
        $this->assertTrue($this->checkState('9dc100d57ca4b13580cd101583e78059
objects/emptydata: 
'));

        $result = $objectStorage->get(
            [
                'key' => 'emptydata',
                'result' => ['body']
            ]
        );
        $this->assertTrue($result === array(
            'body' => '',
        ));

        $result = $objectStorage->get(
            [
                'key' => 'emptydata',
                'result' => []
            ]
        );
        $this->assertTrue($result === []);

        $result = $objectStorage->exists(
            [
                'key' => 'emptydata'
            ]
        );
        $this->assertTrue($result === true);

        $objectStorage->delete(
            [
                'key' => 'emptydata'
            ]
        );
        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
        $result = $objectStorage->get(
            [
                'key' => 'emptydata',
                'result' => ['body']
            ]
        );
        $this->assertTrue($result === null);

        $result = $objectStorage->exists(
            [
                'key' => 'emptydata'
            ]
        );
        $this->assertTrue($result === false);
    }

    /**
     * 
     */
    public function testBreakMetadata()
    {
        $objectStorage = $this->getInstance();

        $objectStorage->set(
            [
                'key' => 'key1',
                'body' => 'body1',
                'metadata.key1' => 'value1'
            ]
        );
        $this->assertTrue($this->checkState('429d57556edd45e292527bbf97befb3e
metadata/key1: content-type:json

{"key1":"value1"}
objects/key1: body1
'));

        $this->createFile($this->getDataDir() . 'metadata/key1', 'broken');
        $this->assertTrue($this->checkState('844b03e029bc1000f7c198a2a43647b3
metadata/key1: broken
objects/key1: body1
'));

        $result = $objectStorage->get(
            [
                'key' => 'key1',
                'result' => ['body', 'metadata']
            ]
        );
        $this->assertTrue($result === array(
            'body' => 'body1',
        ));
    }

    /**
     * 
     */
    public function testRemoveOldMetadata()
    {
        $objectStorage = $this->getInstance();

        $objectStorage->set(
            [
                'key' => 'data1',
                'body' => 'body1',
                'metadata.var1' => '1',
                'metadata.var2' => '2'
            ]
        );
        $this->assertTrue($this->checkState('dcc62823d8b65f6f72741098df815fb0
metadata/data1: content-type:json

{"var1":"1","var2":"2"}
objects/data1: body1'));

        $objectStorage->set(
            [
                'key' => 'data1',
                'body' => 'body1',
                'metadata.*' => 'old',
                'metadata.var2' => '2+',
                'metadata.var3' => '3'
            ]
        );
        $this->assertTrue($this->checkState('d160e9e3ab4c03852ae7adfe2f3dc3ae
metadata/data1: content-type:json

{"var1":"old","var2":"2+","var3":"3"}
objects/data1: body1'));

        $objectStorage->set(
            [
                'key' => 'data1',
                'body' => 'body1',
                'metadata.*' => '',
                'metadata.var3' => '3+'
            ]
        );
        $this->assertTrue($this->checkState('276612436ecf6bd4c3675fb1f3593e44
metadata/data1: content-type:json

{"var3":"3+"}
objects/data1: body1'));

        //        echo (isset($result) ? "\n\n" . var_export($result) : '') . "\n\n" . $this->getState() . "\n\n";
        //        exit;
    }

    /**
     * 
     */
    public function testWhereOperators()
    {
        $objectStorage = $this->getInstance();

        $objectStorage->set(
            [
                'key' => 'data1',
                'body' => ''
            ]
        );
        $objectStorage->set(
            [
                'key' => 'data2',
                'body' => ''
            ]
        );
        $objectStorage->set(
            [
                'key' => 'data3',
                'body' => ''
            ]
        );

        // Test equal
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'data1', 'equal']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data1'
            )
        ));

        // Test notEqual
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'data1', 'notEqual']
                ],
                'result' => ['key']
            ]
        );

        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data2'
            ),
            1 =>
            array(
                'key' => 'data3'
            )
        ));

        // Test startWith
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'data', 'startWith']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data1'
            ),
            1 =>
            array(
                'key' => 'data2'
            ),
            2 =>
            array(
                'key' => 'data3'
            )
        ));

        // Test notStartWith
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'data2', 'notStartWith']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data1'
            ),
            1 =>
            array(
                'key' => 'data3'
            )
        ));


        // Test endWith
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '2', 'endWith']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data2'
            )
        ));

        // Test notEndWith
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '2', 'notEndWith']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data1'
            ),
            1 =>
            array(
                'key' => 'data3'
            )
        ));

        // Test regExp
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '1', 'regExp']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data1'
            )
        ));

        // Test notRegExp
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', '1', 'notRegExp']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'data2'
            ),
            1 =>
            array(
                'key' => 'data3'
            )
        ));
    }

    /**
     * 
     */
    public function testDeleteAfterSet()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body']
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null
        ));

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testExists()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'exists',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'exists',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'exists',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'append',
                    'key' => 'product-1',
                    'body' => 'value'
                ],
                [
                    'command' => 'exists',
                    'key' => 'product-1'
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => false,
            1 => null,
            2 => true,
            3 => null,
            4 => false,
            5 => null,
            6 => true
        ));

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testBodyLength()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body.length']
                ],
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body.length']
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body.length']
                ],
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 12'
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-1']
                    ],
                    'result' => ['key', 'body', 'body.length']
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 =>
            array(
                'key' => 'product-1',
                'body.length' => 14
            ),
            3 => null,
            4 => null,
            5 => null,
            6 =>
            array(
                array(
                    'key' => 'product-1',
                    'body' => 'product body 12',
                    'body.length' => 15
                )
            ),
            7 => null
        ));

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testBodyRange()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body.length']
                ],
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => '0123456789abcdef'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body', 'body.range(0,12)', 'body.range(3)']
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body.range(0,12)', 'body.range(3)']
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-1']
                    ],
                    'result' => ['key', 'body', 'body.range(0,12)', 'body.range(3)']
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'product-1']
                    ],
                    'result' => ['key', 'body.range(0,12)', 'body.range(3)']
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => NULL,
            1 => NULL,
            2 =>
            array(
                'key' => 'product-1',
                'body' => '0123456789abcdef',
                'body.range(0,12)' => '0123456789ab',
                'body.range(3)' => '3456789abcdef',
            ),
            3 =>
            array(
                'key' => 'product-1',
                'body.range(0,12)' => '0123456789ab',
                'body.range(3)' => '3456789abcdef',
            ),
            4 =>
            array(
                0 =>
                array(
                    'key' => 'product-1',
                    'body' => '0123456789abcdef',
                    'body.range(0,12)' => '0123456789ab',
                    'body.range(3)' => '3456789abcdef',
                ),
            ),
            5 =>
            array(
                0 =>
                array(
                    'key' => 'product-1',
                    'body.range(0,12)' => '0123456789ab',
                    'body.range(3)' => '3456789abcdef',
                ),
            ),
            6 => NULL,
        ));

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testAppendAfterDelete()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'delete',
                    'key' => 'product-1'
                ],
                [
                    'command' => 'append',
                    'key' => 'product-1',
                    'body' => 'product body 2'
                ],
                [
                    'command' => 'get',
                    'key' => 'product-1',
                    'result' => ['key', 'body']
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null,
            3 => array(
                'key' => 'product-1',
                'body' => 'product body 2',
            ),
        ));

        $this->assertTrue($this->checkState('89dadbb5027a7c8479fd981c0861b6a8
objects/product-1: product body 2
'));
    }

    /**
     * 
     */
    public function testRenameAfterDelete()
    {
        $objectStorage = $this->getInstance();
        $exceptionCaught = false;
        try {
            $objectStorage->execute(
                [
                    [
                        'command' => 'set',
                        'key' => 'product-1',
                        'body' => 'product body 1'
                    ],
                    [
                        'command' => 'delete',
                        'key' => 'product-1'
                    ],
                    [
                        'command' => 'rename',
                        'sourceKey' => 'product-1',
                        'targetKey' => 'product-2',
                    ]
                ]
            );
        } catch (\IvoPetkov\ObjectStorage\ObjectNotFoundException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught);

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testDuplicateAfterDelete()
    {
        $objectStorage = $this->getInstance();
        try {
            $objectStorage->execute(
                [
                    [
                        'command' => 'set',
                        'key' => 'product-1',
                        'body' => 'product body 1'
                    ],
                    [
                        'command' => 'delete',
                        'key' => 'product-1'
                    ],
                    [
                        'command' => 'duplicate',
                        'sourceKey' => 'product-1',
                        'targetKey' => 'product-2',
                    ]
                ]
            );
        } catch (\IvoPetkov\ObjectStorage\ObjectNotFoundException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught);

        $this->assertTrue($this->checkState('d41d8cd98f00b204e9800998ecf8427e
'));
    }

    /**
     * 
     */
    public function testRename()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1a',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-2a',
                    'body' => 'product body 2',
                    'metadata.key' => 'value'
                ],
                [
                    'command' => 'rename',
                    'sourceKey' => 'product-1a',
                    'targetKey' => 'product-1b',
                ],
                [
                    'command' => 'rename',
                    'sourceKey' => 'product-2a',
                    'targetKey' => 'product-2b',
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null,
            3 => null,
        ));

        $this->assertTrue($this->checkState('dd8b80a39b4400501586f368aa8299de
metadata/product-2b: content-type:json

{"key":"value"}
objects/product-1b: product body 1
objects/product-2b: product body 2'));
    }

    /**
     * 
     */
    public function testDuplicate()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1a',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-2a',
                    'body' => 'product body 2',
                    'metadata.key' => 'value'
                ],
                [
                    'command' => 'duplicate',
                    'sourceKey' => 'product-1a',
                    'targetKey' => 'product-1b',
                ],
                [
                    'command' => 'duplicate',
                    'sourceKey' => 'product-2a',
                    'targetKey' => 'product-2b',
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null,
            3 => null,
        ));

        $this->assertTrue($this->checkState('5e08ed302b17012d025d13faa4767a5d
metadata/product-2a: content-type:json

{"key":"value"}
metadata/product-2b: content-type:json

{"key":"value"}
objects/product-1a: product body 1
objects/product-1b: product body 1
objects/product-2a: product body 2
objects/product-2b: product body 2'));
    }

    /**
     * 
     */
    public function testSearchBody()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1a',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-2a',
                    'body' => 'product body 2',
                    'metadata.key' => 'value'
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['body', 'body 2', 'contain']
                    ],
                    'result' => ['key', 'body']
                ]
            ]
        );
        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => array(
                0 =>
                array(
                    'key' => 'product-2a',
                    'body' => 'product body 2',
                ),
            ),
        ));

        $this->assertTrue($this->checkState('a7091f03e727d073600c5f48b47d0399
metadata/product-2a: content-type:json

{"key":"value"}
objects/product-1a: product body 1
objects/product-2a: product body 2'));
    }

    /**
     * 
     */
    public function testSearchLimit()
    {
        $objectStorage = $this->getInstance();
        $result = $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-2',
                    'body' => 'product body 2'
                ],
                [
                    'command' => 'set',
                    'key' => 'services/service-1',
                    'body' => 'service body 1'
                ],
                [
                    'command' => 'set',
                    'key' => 'services/service-2',
                    'body' => 'service body 2'
                ],
                [
                    'command' => 'search',
                    'result' => ['key'],
                    'limit' => 0
                ],
                [
                    'command' => 'search',
                    'result' => ['key'],
                    'limit' => 1
                ],
                [
                    'command' => 'search',
                    'result' => ['key'],
                    'limit' => 3
                ],
                [
                    'command' => 'search',
                    'result' => ['key'],
                    'limit' => 5
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'services/', 'startWith']
                    ],
                    'result' => ['key'],
                    'limit' => 0
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'services/', 'startWith']
                    ],
                    'result' => ['key'],
                    'limit' => 1
                ],
                [
                    'command' => 'search',
                    'where' => [
                        ['key', 'services/', 'startWith']
                    ],
                    'result' => ['key'],
                    'limit' => 3
                ]
            ]
        );

        $this->assertTrue($result === array(
            0 => null,
            1 => null,
            2 => null,
            3 => null,
            4 =>
            array(),
            5 =>
            array(
                0 =>
                array(
                    'key' => 'product-1',
                ),
            ),
            6 =>
            array(
                0 =>
                array(
                    'key' => 'product-1',
                ),
                1 =>
                array(
                    'key' => 'product-2',
                ),
                2 =>
                array(
                    'key' => 'services/service-1',
                ),
            ),
            7 =>
            array(
                0 =>
                array(
                    'key' => 'product-1',
                ),
                1 =>
                array(
                    'key' => 'product-2',
                ),
                2 =>
                array(
                    'key' => 'services/service-1',
                ),
                3 =>
                array(
                    'key' => 'services/service-2',
                ),
            ),
            8 =>
            array(),
            9 =>
            array(
                0 =>
                array(
                    'key' => 'services/service-1',
                ),
            ),
            10 =>
            array(
                0 =>
                array(
                    'key' => 'services/service-1',
                ),
                1 =>
                array(
                    'key' => 'services/service-2',
                ),
            ),
        ));

        $this->assertTrue($this->checkState('216bdc8b247a34135dd08e9172e6a6f2
objects/product-1: product body 1
objects/product-2: product body 2
objects/services/service-1: service body 1
objects/services/service-2: service body 2'));
    }

    /**
     * 
     */
    public function testClearMetadata()
    {
        $objectStorage = $this->getInstance();
        $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1',
                    'metadata.key1' => 'value1'
                ],
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'metadata.*' => ''
                ],
            ]
        );
        $this->assertTrue($this->checkState('f66d138ab8ae4cc8fc6a34e9fa59b19f
objects/product-1: product body 1
'));


        $objectStorage->execute(
            [
                [
                    'command' => 'set',
                    'key' => 'product-1',
                    'body' => 'product body 1',
                    'metadata.*' => ''
                ],
            ]
        );
        $this->assertTrue($this->checkState('f66d138ab8ae4cc8fc6a34e9fa59b19f
objects/product-1: product body 1
'));
    }

    public function testSearchForDuplicates()
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
        $result = $objectStorage->search(
            [
                'where' => [
                    ['key', 'book/', 'startWith'],
                    ['key', 'book/b', 'startWith']
                ],
                'result' => ['key']
            ]
        );
        $this->assertTrue($result === array(
            0 =>
            array(
                'key' => 'book/book1'
            ),
            1 =>
            array(
                'key' => 'book/book2'
            ),
        ));
    }
}
