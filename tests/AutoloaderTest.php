<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) 2016 Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class AutoloaderTest extends ObjectStorageAutoloaderTestCase
{

    /**
     * 
     */
    public function testClasses()
    {
        $this->assertTrue(class_exists('IvoPetkov\ObjectStorage'));
        $this->assertTrue(class_exists('IvoPetkov\ObjectStorage\ErrorException'));
        $this->assertTrue(class_exists('IvoPetkov\ObjectStorage\ObjectLockedException'));
    }

}
