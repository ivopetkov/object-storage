<?php

/**
 * 
 */
class DataTest extends PHPUnit_Framework_TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testDataState1()
    {
        removeDir('data/');
        $objectStorage = new ObjectStorage('data/');

        // Create initial data
        $result = $objectStorage->set(
                [
                    'key' => 'book-1449392776',
                    'body' => 'book 1449392776 content in pdf format',
                    'metadata.title' => 'Programming PHP',
                    'metadata.authors' => '["Kevin Tatroe", "Peter MacIntyre", "Rasmus Lerdorf"]',
                    'metadata.year' => '2013'
                ]
        );
        $this->assertTrue($result);
        $result = $objectStorage->set(
                [
                    'key' => 'book-1430260319',
                    'body' => 'book 1430260319 content in pdf format',
                    'metadata.title' => 'PHP Objects, Patterns, and Practice',
                    'metadata.authors' => '["Matt Zandstra"]',
                    'metadata.year' => '2013'
                ]
        );
        $this->assertTrue($result);
        $result = $objectStorage->set(
                [
                    'key' => 'book-1430268158',
                    'body' => 'book 1430268158 content in pdf format',
                    'metadata.title' => 'PHP for Absolute Beginners',
                    'metadata.authors' => '["Jason Lengstorf", "Thomas Blom Hansen"]',
                    'metadata.year' => '2014'
                ]
        );
        $this->assertTrue($result);
        $result = $objectStorage->set(
                [
                    'key' => 'book-1000000000'
                ]
        );
        $this->assertTrue($result);
        $result = $objectStorage->set(
                [
                    'key' => 'book-2000000000',
                    'body' => 'book 2000000000 content in pdf format'
                ]
        );
        $this->assertTrue($result);
        $result = $objectStorage->set(
                [
                    'key' => 'books/3000000000',
                    'body' => 'book 3000000000 content in pdf format',
                    'metadata.year' => '2014'
                ]
        );
        $this->assertTrue($result);
        $this->assertTrue(checkState('ed20ab8c4a519ca9fc10608331cd8bc1
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
                        ['key', ['book-1449392776', 'book-1430268158']]
                    ],
                    'result' => ['key', 'body', 'metadata.title', 'metadata.year']
                ]
        );
        $this->assertTrue($result ===
                array(
                    0 =>
                    array(
                        'key' => 'book-1449392776',
                        'body' => 'book 1449392776 content in pdf format',
                        'metadata.title' => 'Programming PHP',
                        'metadata.year' => '2013',
                    ),
                    1 =>
                    array(
                        'key' => 'book-1430268158',
                        'body' => 'book 1430268158 content in pdf format',
                        'metadata.title' => 'PHP for Absolute Beginners',
                        'metadata.year' => '2014',
                    ),
        ));
        $this->assertTrue(checkState(
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
'));



        // Set (update metadata)
        $result = $objectStorage->set(
                [
                    'key' => 'book-1449392776',
                    'metadata.rating' => '3.4'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState(
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
'));



        // Get
        $result = $objectStorage->get(
                [
                    'key' => 'book-1449392776',
                    'result' => ['metadata.rating']
                ]
        );
        $this->assertTrue($result ===
                array(
                    'metadata.rating' => '3.4',
        ));
        $this->assertTrue(checkState(
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
'));


        // Set
        $result = $objectStorage->set(
                [
                    'key' => 'book-1449392776-comments',
                    'body' => "John Smith: This book is awaseome.\n"
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState(
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
'));



        // Append
        $result = $objectStorage->append(
                [
                    'key' => 'book-1449392776-comments',
                    'body' => "Oliver Mark: Best book I've ever read.\n"
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState(
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
'));



        // delete
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1449392776-comments'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState(
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
'));



        // append
        $result = $objectStorage->append(
                [
                    'key' => 'book-1000000000-comments',
                    'body' => "Ivo Petkov: Great!.\n",
                    'metadata.ivo' => "test"
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState(
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
'));


        // search
        $result = $objectStorage->search(
                [
                    'where' => [
                        ['metadata.year', '2013']
                    ],
                    'result' => ['key', 'metadata.title', 'metadata.authors']
                ]
        );
        $this->assertTrue($result ===
                array(
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
        $this->assertTrue(checkState('65c9e2014d2e54752d99a49f56c3fe1e
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
        $this->assertTrue(checkState('65c9e2014d2e54752d99a49f56c3fe1e
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
        $result = $objectStorage->duplicate(
                [
                    'sourceKey' => 'book-1449392776',
                    'targetKey' => 'book-1449392776-copy'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('9b0ef8154be7ef0b7b939f7ca2e9af77
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
        $result = $objectStorage->rename(
                [
                    'sourceKey' => 'book-1449392776',
                    'targetKey' => 'book-5000000000'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('8171d7214894ef37665cd74576bde01f
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
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1000000000'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1000000000-comments'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1449392776'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1430260319'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1430268158'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-2000000000'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'books/3000000000'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-1449392776-copy'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'book-5000000000'
                ]
        );
        $this->assertTrue($result === true);

        $this->assertTrue(checkState('d41d8cd98f00b204e9800998ecf8427e
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
            0 => true,
            1 => true,
            2 => true,
            3 => true,
            4 => true,
            5 =>
            array(
                'key' => 'product-5',
                'body' => 'product body 1',
            ),
            6 => true,
            7 =>
            array(
                0 =>
                array(
                    'key' => 'product-5',
                    'body' => 'product body 2',
                    'metadata.ivo' => '2011',
                ),
            ),
            8 => true,
            9 =>
            array(
            ),
            10 => true,
            11 =>
            array(
                0 =>
                array(
                    'key' => 'product-5',
                    'body' => 'product body 3',
                ),
            ),
            12 =>
            array(
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
            13 => true,
            14 => true,
        ));
        $this->assertTrue(checkState('d41d8cd98f00b204e9800998ecf8427e
'));



        // test delete metadata
        $result = $objectStorage->set(
                [
                    'key' => 'test-delete-metadata',
                    'metadata.rating' => '3.4'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('e6ff92ea7fb169ba3e87676f3bbc379e
metadata/test-delete-metadata: content-type:json

{"rating":"3.4"}
objects/test-delete-metadata: 
'));

        $result = $objectStorage->set(
                [
                    'key' => 'test-delete-metadata',
                    'metadata.rating' => ''
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('366ff7633ebcec69cf1954175e4aa7fc
objects/test-delete-metadata: '));

        $result = $objectStorage->delete(
                [
                    'key' => 'test-delete-metadata'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('d41d8cd98f00b204e9800998ecf8427e
'));



        // regexp search
        $result = $objectStorage->set(
                [
                    'key' => 'prefix1/dataa',
                    'body' => 'A'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->set(
                [
                    'key' => 'prefix1/datab',
                    'body' => 'B'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->set(
                [
                    'key' => 'prefix2/datac',
                    'body' => 'C'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('f34e94bee39fd95da85fc91a3ca302ea
objects/prefix1/dataa: A
objects/prefix1/datab: B
objects/prefix2/datac: C
'));

        $result = $objectStorage->search(
                [
                    'where' => [
                        ['key', '^prefix1\/', 'regexp']
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
        $this->assertTrue(checkState('f34e94bee39fd95da85fc91a3ca302ea
objects/prefix1/dataa: A
objects/prefix1/datab: B
objects/prefix2/datac: C
'));

        $result = $objectStorage->delete(
                [
                    'key' => 'prefix1/dataa'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'prefix1/datab'
                ]
        );
        $this->assertTrue($result === true);
        $result = $objectStorage->delete(
                [
                    'key' => 'prefix2/datac'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('d41d8cd98f00b204e9800998ecf8427e
'));


        removeDir('data/');
    }

    /**
     * @runInSeparateProcess
     */
    public function testEmptyData()
    {
        removeDir('data/');
        $objectStorage = new ObjectStorage('data/');

        $result = $objectStorage->set(
                [
                    'key' => 'emptydata',
                    'body' => ''
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('9dc100d57ca4b13580cd101583e78059
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

        $result = $objectStorage->delete(
                [
                    'key' => 'emptydata'
                ]
        );
        $this->assertTrue($result === true);
        $this->assertTrue(checkState('d41d8cd98f00b204e9800998ecf8427e
'));
        $result = $objectStorage->get(
                [
                    'key' => 'emptydata',
                    'result' => ['body']
                ]
        );
        $this->assertTrue($result === array());

        //echo "\n\n" . var_export($result) . "\n\n" . getState() . "\n\n";

        removeDir('data/');
    }

}
