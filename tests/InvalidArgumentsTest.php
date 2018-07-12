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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
        $this->removeDataDir();
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
