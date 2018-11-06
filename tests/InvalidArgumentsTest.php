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
class InvalidArgumentsTest extends ObjectStorageTestCase
{

    /**
     * 
     */
    public function testInvalidKey1()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '.',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey2()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '..',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey3()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '/',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey4()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => './key',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey5()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '../key',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey6()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'key/.',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey7()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'key/..',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey8()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '/key',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey9()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'key/',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey10()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'key/./key',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey11()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'key/../key',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey12()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'invalid?',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidKey13()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => '',
                    'data' => 'data'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidMetadata1()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'test',
                    'metadata.' => '1'
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidMetadata2()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'test',
                    'metadata.name' => 1
                ]
        );
    }

    /**
     * 
     */
    public function testInvalidMetadata3()
    {
        $objectStorage = $this->getInstance();
        $this->expectException('InvalidArgumentException');
        $objectStorage->set(
                [
                    'key' => 'test',
                    'metadata.na$me' => '1'
                ]
        );
    }

}
