<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

class ObjectStorage
{

    /**
     * The current library version
     */
    const VERSION = '0.3.5';

    /**
     * The directory where the objects will be stored
     * 
     * @var string 
     */
    public $objectsDir = null;

    /**
     * The directory where the objects metadata will be stored
     * 
     * @var string 
     */
    public $metadataDir = null;

    /**
     * The directory where temp library data will be stored
     * 
     * @var string 
     */
    public $tempDir = null;

    /**
     * Number of retries to make when waiting for locked (accessed by other scripts) objects
     * 
     * @var int 
     */
    public $lockRetriesCount = 3;

    /**
     * Time (in microseconds) between retries when waiting for locked objects
     * 
     * @var int 
     */
    public $lockRetryDelay = 500000; // microseconds

    /**
     * Creates a new Object storage instance
     * 
     * @param string $dir The directory where the library will store data (the objects, the metadata and the temporary files)
     */

    public function __construct(string $dir = 'data/')
    {
        $dir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
        $this->objectsDir = $dir . 'objects' . DIRECTORY_SEPARATOR;
        $this->metadataDir = $dir . 'metadata' . DIRECTORY_SEPARATOR;
        $this->tempDir = $dir . 'temp' . DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieves object data for a specified key
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'result' => ['body', 'metadata.year']]
     * @return array An array containing the result data if existent, empty array otherwise
     */
    public function get(array $parameters): array
    {
        return $this->executeCommand([$parameters], 'get')[0];
    }

    /**
     * Saves object data for a specified key
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1', 'metadata.year' => '2000']. Specifying metadata.* will bulk remove/update all previous metadata.
     * @return void No value is returned
     */
    public function set(array $parameters): void
    {
        $this->executeCommand([$parameters], 'set')[0];
    }

    /**
     * Appends object data for a specified key. The object will be created if not existent.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1']
     * @return void No value is returned
     */
    public function append(array $parameters): void
    {
        $this->executeCommand([$parameters], 'append')[0];
    }

    /**
     * Creates a copy of an object. It's metadata is copied too.
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return void No value is returned
     */
    public function duplicate(array $parameters): void
    {
        $this->executeCommand([$parameters], 'duplicate')[0];
    }

    /**
     * Renames an object
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return void No value is returned
     */
    public function rename(array $parameters): void
    {
        $this->executeCommand([$parameters], 'rename')[0];
    }

    /**
     * Deletes an object and it's metadata
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1']
     * @return void No value is returned
     */
    public function delete(array $parameters): void
    {
        $this->executeCommand([$parameters], 'delete')[0];
    }

    /**
     * Retrieves a list of all object matching the criteria specified
     * 
     * @param array $parameters Data in the following format:
     *    // Finds objects by key 
     *    [
     *        'where' => [
     *            ['key', ['book-1449392776', 'book-1430268158']]
     *        ],
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     *    // Finds objects by metadata 
     *    [
     *        'where' => [
     *            ['metadata.year', '2013']
     *        ],
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     *    // Finds objects by regular expression 
     *    [
     *        'where' => [
     *            ['key', '^prefix1\/', 'regexp']
     *        ],
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     * @return array An array containing all matching objects
     */
    public function search(array $parameters): array
    {
        return $this->executeCommand([$parameters], 'search')[0];
    }

    /**
     * Executes single command
     * 
     * @param array $parameters The command parameters
     * @param string $command The command name
     * @return mixed
     */
    private function executeCommand(array $parameters, string $command)
    {
        foreach ($parameters as $index => $object) {
            $parameters[$index]['command'] = $command;
        }
        $result = $this->execute($parameters);
        if (isset($index)) {
            unset($index);
        }
        if (isset($object)) {
            unset($object);
        }
        unset($parameters);
        unset($command);
        return $result;
    }

    /**
     * Checks whether the key specified is valid
     * 
     * @param string $key The key to check
     * @return boolean TRUE if the key is valid, FALSE otherwise
     */
    public function isValidKey($key): bool
    {
        if (!is_string($key) || strlen($key) === 0 || $key === '.' || $key === '..' || strpos($key, '/../') !== false || strpos($key, '/./') !== false || strpos($key, '/') === 0 || strpos($key, './') === 0 || strpos($key, '../') === 0 || substr($key, -2) === '/.' || substr($key, -3) === '/..' || substr($key, -1) === '/') {
            return false;
        }
        return preg_match("/^[a-z0-9\.\/\-\_]*$/", $key) === 1;
    }

    /**
     * Executes list of commands
     * 
     * @param array $commands Array containing list of commands in the following format:
     *    [
     *        'command' => 'set',
     *        'key' => 'example1',
     *        'body' => 'body1'
     *    ],
     *    [
     *        'command' => 'append',
     *        'key' => 'example2',
     *        'body' => 'body2'
     *    ]
     * @return array Array containing the results for the commands
     * @throws \InvalidArgumentException
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function execute(array $commands): array
    {
        $filePointers = [];
        $filesToDelete = [];
        $emptyOpenedFiles = []; // opened but no content set

        $encodeMetaData = function($metadata) {
            return "content-type:json\n\n" . json_encode($metadata);
        };

        $decodeMetadata = function ($metadata) {
            if (!isset($metadata{0})) {
                return [];
            }
            $parts = explode("\n\n", $metadata, 2);
            if (!isset($parts[1]) || $parts[0] !== 'content-type:json') {
                return [];
            }
            $result = json_decode($parts[1], true);
            return is_array($result) ? $result : [];
        };

        $isValidMetadata = function($key, $value, $allowWildcard) {
            if ($allowWildcard && $key === 'metadata.*') {
                return true;
            }
            if (substr($key, 0, 9) !== 'metadata.' || $key === 'metadata.') {
                return false;
            }
            if (!is_string($value)) {
                return false;
            }
            return preg_match("/^[a-zA-Z0-9\.\-\_]*$/", $key) === 1;
        };

        $areWhereConditionsMet = function($value, $conditions) {
            foreach ($conditions as $conditionData) {
                if ($conditionData[0] === '==') {
                    if ($value === $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'equal') {
                    if ($value === $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'notEqual') {
                    if ($value !== $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'regexp' || $conditionData[0] === 'regExp') {
                    if (preg_match('/' . $conditionData[1] . '/', $value) === 1) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'notRegExp') {
                    if (preg_match('/' . $conditionData[1] . '/', $value) === 0) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'startsWith' || $conditionData[0] === 'startWith') {
                    if (substr($value, 0, strlen($conditionData[1])) === $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'notStartWith') {
                    if (substr($value, 0, strlen($conditionData[1])) !== $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'endWith') {
                    if (substr($value, -strlen($conditionData[1])) === $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'notEndWith') {
                    if (substr($value, -strlen($conditionData[1])) !== $conditionData[1]) {
                        return true;
                    }
                } elseif ($conditionData[0] === 'search') {
                    if (strpos(strtolower($value), strtolower($conditionData[1])) !== false) {
                        return true;
                    }
                }
            }
            return false;
        };

        $prepareFileForWriting = function($filename) use (&$filePointers, &$emptyOpenedFiles) {
            if (isset($filePointers[$filename])) {
                return;
            }
            if (is_dir($filename)) {
                throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not writable (dir with the same name exists).');
            }
            if ($this->createFileDirIfNotExists($filename) === false) {
                throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not writable (cannot create dir).');
            }
            $getFilePointer = function() use ($filename, &$emptyOpenedFiles) {
                clearstatcache(false, $filename);
                $isNew = !is_file($filename);
                $filePointer = fopen($filename, "c+");
                if ($filePointer !== false) {
                    $flockResult = flock($filePointer, LOCK_EX | LOCK_NB);
                    if ($flockResult !== false) {
                        if ($isNew) {
                            $emptyOpenedFiles[$filename] = 1;
                        }
                        return $filePointer;
                    } else {
                        fclose($filePointer);
                    }
                }
                return false;
            };
            $done = false;
            for ($i = 0; $i < $this->lockRetriesCount; $i++) {
                $filePointer = $getFilePointer();
                if ($filePointer !== false) {
                    $filePointers[$filename] = $filePointer;
                    $done = true;
                    break;
                }
                if ($i < $this->lockRetriesCount - 1) {
                    usleep($this->lockRetryDelay);
                }
            }
            if (!$done) {
                throw new \IvoPetkov\ObjectStorage\ObjectLockedException('The file ' . $filename . ' is locked and cannot be open for writing.');
            }
        };

        $prepareFileForReading = function($filename, $required = false) use (&$filePointers) {
            if (isset($filePointers[$filename])) {
                return true;
            }
            if (is_file($filename)) {
                if (!is_readable($filename)) {
                    throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not readable.');
                }
            } else {
                if ($required) {
                    throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' does not exist.');
                }
                $rootDir = dirname($filename, 100);
                $isParentDirReadable = false;
                for ($i = 1; $i < 100; $i++) {
                    $dirToCheck = dirname($filename, $i);
                    if (is_dir($dirToCheck) && is_readable($dirToCheck)) {
                        $isParentDirReadable = true;
                        break;
                    }
                    if ($dirToCheck === $rootDir) {
                        break;
                    }
                }
                if (!$isParentDirReadable) {
                    throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not readable (parent dir is not readable).');
                }
            }
        };

        $setFileContent = function($filename, $content) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
            if (isset($filePointers[$filename])) {
                if (isset($filesToDelete[$filename])) {
                    unset($filesToDelete[$filename]);
                }
                $filePointer = $filePointers[$filename];
                ftruncate($filePointer, 0);
                fseek($filePointer, 0);
                fwrite($filePointer, $content);
                if (isset($emptyOpenedFiles[$filename])) {
                    unset($emptyOpenedFiles[$filename]);
                }
            } else {
                throw new \Exception('Internal error! File ' . $filename . ' is not opened for writing! Should not get here!');
            }
        };

        $appendFileContent = function($filename, $content) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
            if (isset($filePointers[$filename])) {
                $filePointer = $filePointers[$filename];
                if (isset($filesToDelete[$filename])) {
                    unset($filesToDelete[$filename]);
                    ftruncate($filePointer, 0);
                    fseek($filePointer, 0);
                }
                fseek($filePointer, 0, SEEK_END);
                fwrite($filePointer, $content);
                if (isset($emptyOpenedFiles[$filename])) {
                    unset($emptyOpenedFiles[$filename]);
                }
            } else {
                throw new \Exception('Internal error! File ' . $filename . ' is not opened for writing! Should not get here!');
            }
        };

        $getFileContent = function($filename) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
            if (isset($filesToDelete[$filename])) {
                return null;
            }
            if (isset($emptyOpenedFiles[$filename])) {
                return null;
            }
            if (isset($filePointers[$filename])) {
                $filePointer = $filePointers[$filename];
                $pointerPosition = ftell($filePointer);
                fseek($filePointer, 0);
                $content = '';
                while (!feof($filePointer)) {
                    $content .= fread($filePointer, 8192);
                }
                fseek($filePointer, $pointerPosition);
                return $content;
            } else {
                if (is_file($filename)) {
                    $filePointer = fopen($filename, "r");
                    flock($filePointer, LOCK_SH);
                    $content = '';
                    while (!feof($filePointer)) {
                        $content .= fread($filePointer, 8192);
                    }
                    flock($filePointer, LOCK_UN);
                    fclose($filePointer);
                    return $content;
                }
                return null;
            }
        };

        $deleteFile = function($filename) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
            if (isset($filePointers[$filename])) {
                $filePointer = $filePointers[$filename];
                ftruncate($filePointer, 0);
                fseek($filePointer, 0);
            }
            $filesToDelete[$filename] = 1;
            if (isset($emptyOpenedFiles[$filename])) {
                unset($emptyOpenedFiles[$filename]);
            }
        };

        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            restore_error_handler();
            throw new \IvoPetkov\ObjectStorage\ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        $functions = [];
        $thrownException = null;
        try {
            foreach ($commands as $index => $commandData) {

                $getProperty = function($name, $required = false) use ($index, $commandData, $isValidMetadata) {
                    if ($name === 'command') {
                        if (isset($commandData['command'])) {
                            if (!is_string($commandData['command'])) {
                                throw new \InvalidArgumentException('The command property must be of type string for item[' . $index . ']');
                            }
                            return $commandData['command'];
                        }
                    } elseif ($name === 'key') {
                        if (isset($commandData['key'])) {
                            if (!is_string($commandData['key'])) {
                                throw new \InvalidArgumentException('The key property must be of type string for item[' . $index . ']');
                            }
                            if (!$this->isValidKey($commandData['key'])) {
                                throw new \InvalidArgumentException('The key property is not valid for item[' . $index . ']');
                            }
                            return $commandData['key'];
                        }
                    } elseif ($name === 'body') {
                        if (isset($commandData['body'])) {
                            if (!is_string($commandData['body'])) {
                                throw new \InvalidArgumentException('The body property must be of type string for item[' . $index . ']');
                            }
                            return $commandData['body'];
                        }
                    } elseif ($name === 'sourceKey') {
                        if (isset($commandData['sourceKey'])) {
                            if (!is_string($commandData['sourceKey'])) {
                                throw new \InvalidArgumentException('The sourceKey property must be of type string for item[' . $index . ']');
                            }
                            if (!$this->isValidKey($commandData['sourceKey'])) {
                                throw new \InvalidArgumentException('The sourceKey property is not valid for item[' . $index . ']');
                            }
                            return $commandData['sourceKey'];
                        }
                    } elseif ($name === 'targetKey') {
                        if (isset($commandData['targetKey'])) {
                            if (!is_string($commandData['targetKey'])) {
                                throw new \InvalidArgumentException('The targetKey property must be of type string for item[' . $index . ']');
                            }
                            if (!$this->isValidKey($commandData['targetKey'])) {
                                throw new \InvalidArgumentException('The targetKey property is not valid for item[' . $index . ']');
                            }
                            return $commandData['targetKey'];
                        }
                    } elseif ($name === 'metadata.*') {
                        $result = [];
                        foreach ($commandData as $commandDataKey => $commandDataValue) {
                            if (substr($commandDataKey, 0, 9) === 'metadata.') {
                                if (!$isValidMetadata($commandDataKey, $commandDataValue, true)) {
                                    throw new \InvalidArgumentException('The metadata key (' . $commandDataKey . ') is not valid for item[' . $index . ']');
                                }
                                $result[substr($commandDataKey, 9)] = $commandDataValue;
                            }
                        }
                        if (!empty($result)) {
                            return $result;
                        }
                    } elseif ($name === 'where') {
                        if (isset($commandData['where'])) {
                            if (!is_array($commandData['where'])) {
                                throw new \InvalidArgumentException('The where property must be of type array for item[' . $index . ']');
                            }
                            $result = [];
                            foreach ($commandData['where'] as $whereItem) {
                                $valid = false;
                                if (is_array($whereItem) && isset($whereItem[0], $whereItem[1]) && is_string($whereItem[0])) {
                                    $whereKey = $whereItem[0];
                                    $whereValue = $whereItem[1];
                                    if ($whereKey !== 'key' && $whereKey !== 'body' && substr($whereKey, 0, 9) !== 'metadata.') {
                                        throw new \InvalidArgumentException('Invalid where key ' . $whereKey . '.');
                                    }
                                    if (!isset($result[$whereKey])) {
                                        $result[$whereKey] = [];
                                    }
                                    $whereOperator = isset($whereItem[2]) ? $whereItem[2] : '==';
                                    if (array_search($whereOperator, ['==', 'regexp', 'search', 'startsWith', 'equal', 'notEqual', 'regExp', 'notRegExp', 'startWith', 'notStartWith', 'endWith', 'notEndWith']) === false) {
                                        throw new \InvalidArgumentException('Invalid where operator ' . $whereOperator . '.');
                                    }
                                    if (is_string($whereValue)) {
                                        $result[$whereKey][] = [$whereOperator, $whereValue];
                                        $valid = true;
                                    } elseif (is_array($whereValue)) {
                                        $valid = true;
                                        foreach ($whereValue as $whereValueItem) {
                                            if (!is_string($whereValueItem)) {
                                                $valid = false;
                                                break;
                                            }
                                        }
                                        if ($valid) {
                                            $temp = array_unique($whereValue);
                                            foreach ($temp as $whereItemValue) {
                                                $result[$whereKey][] = [$whereOperator, $whereItemValue];
                                            }
                                        }
                                    }
                                }
                                if (!$valid) {
                                    throw new \InvalidArgumentException('Where data not valid.');
                                }
                            }

                            if (isset($result['key'])) {
                                foreach ($result['key'] as $whereKeyData) {
                                    $whereOperator = $whereKeyData[0];
                                    if ($whereOperator === '==' || $whereOperator === 'equal') {
                                        if (!$this->isValidKey($whereKeyData[1])) {
                                            throw new \InvalidArgumentException('The key value in where data is not valid.');
                                        }
                                    }
                                }
                            }
                            foreach ($result as $whereItemKey => $whereItemData) {
                                if (substr($whereItemKey, 0, 9) === 'metadata.') {
                                    if (!$isValidMetadata($whereItemKey, '', false)) {
                                        throw new \InvalidArgumentException('The metadata key (' . $whereItemKey . ') in where data is not valid.');
                                    }
                                }
                            }
                            return $result;
                        }
                    } elseif ($name === 'result') {
                        if (isset($commandData['result'])) {
                            if (!is_array($commandData['result'])) {
                                throw new \InvalidArgumentException('The result property must be of type array for item[' . $index . ']');
                            }
                            return $commandData['result'];
                        }
                    } elseif ($name === 'result.metadata.*') {
                        if (isset($commandData['result'])) {
                            if (!is_array($commandData['result'])) {
                                throw new \InvalidArgumentException('The result property must be of type array for item[' . $index . ']');
                            }
                            $resultKeys = isset($commandData['result']) ? $commandData['result'] : [];
                            $result = [];
                            foreach ($resultKeys as $resultCode) {
                                if (substr($resultCode, 0, 9) === 'metadata.') {
                                    $result[] = substr($resultCode, 9);
                                    if (!$isValidMetadata($resultCode, '', false)) {
                                        throw new \InvalidArgumentException('The metadata result key (' . $resultCode . ') is not valid for item[' . $index . ']');
                                    }
                                }
                            }
                            if (!empty($result)) {
                                return $result;
                            }
                        }
                    } else {
                        throw new \InvalidArgumentException('Invalid property ' . $name . ' for item[' . $index . ']');
                    }
                    if ($required) {
                        throw new \InvalidArgumentException('The ' . $name . ' property is required for item[' . $index . ']');
                    }
                    return null;
                };

                $command = $getProperty('command', true);

                if ($command === 'set') {
                    $key = $getProperty('key', true);
                    $body = $getProperty('body');
                    $metadata = $getProperty('metadata.*');
                    $modifyBody = $body !== null;
                    $modifyMetadata = $metadata !== null;
                    if ($modifyBody) {
                        $prepareFileForWriting($this->objectsDir . $key);
                    }
                    if ($modifyMetadata && sizeof($metadata) === 1 && isset($metadata['*']) && $metadata['*'] === '') { // Check for setting empty metadata
                        if (!isset($filePointers[$this->metadataDir . $key]) && !is_file($this->metadataDir . $key)) { // Not opened for writing and does not exists
                            $modifyMetadata = false;
                        }
                    }
                    if ($modifyMetadata) {
                        if (!$modifyBody) { // Used for the lock
                            $prepareFileForWriting($this->objectsDir . $key);
                        }
                        $prepareFileForWriting($this->metadataDir . $key);
                    }

                    $functions[$index] = function() use ($key, $body, $metadata, $modifyBody, $modifyMetadata, $setFileContent, $getFileContent, $deleteFile, $decodeMetadata, $encodeMetaData) {
                        if ($modifyBody) {
                            $setFileContent($this->objectsDir . $key, $body);
                        }
                        if ($modifyMetadata) {
                            $objectMetadata = $decodeMetadata($getFileContent($this->metadataDir . $key));
                            if (isset($metadata['*'])) {
                                foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                    $objectMetadata[$metadataKey] = $metadata['*'];
                                }
                                unset($metadata['*']);
                            }
                            $objectMetadata = array_merge($objectMetadata, $metadata);
                            foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                if ($metadataValue === '') {
                                    unset($objectMetadata[$metadataKey]);
                                }
                            }
                            if (empty($objectMetadata)) {
                                $deleteFile($this->metadataDir . $key);
                            } else {
                                $setFileContent($this->metadataDir . $key, $encodeMetaData($objectMetadata));
                            }
                        }
                        return true;
                    };
                } elseif ($command === 'append') {
                    $key = $getProperty('key', true);
                    $body = $getProperty('body', true);
                    $prepareFileForWriting($this->objectsDir . $key);
                    $functions[$index] = function() use ($key, $body, $appendFileContent) {
                        $appendFileContent($this->objectsDir . $key, $body);
                        return true;
                    };
                } elseif ($command === 'delete') {
                    $key = $getProperty('key', true);
                    $deleteObjectFile = true;
                    if (!isset($filePointers[$this->objectsDir . $key]) && !is_file($this->objectsDir . $key)) { // Not opened for writing and does not exists
                        $deleteObjectFile = false;
                    }
                    $deleteMetadataFile = true;
                    if (!isset($filePointers[$this->metadataDir . $key]) && !is_file($this->metadataDir . $key)) { // Not opened for writing and does not exists
                        $deleteMetadataFile = false;
                    }
                    if ($deleteObjectFile) {
                        $prepareFileForWriting($this->objectsDir . $key);
                    }
                    if ($deleteMetadataFile) {
                        $prepareFileForWriting($this->metadataDir . $key);
                    }
                    $functions[$index] = function() use ($key, $deleteObjectFile, $deleteMetadataFile, $deleteFile) {
                        if ($deleteObjectFile) {
                            $deleteFile($this->objectsDir . $key);
                        }
                        if ($deleteMetadataFile) {
                            $deleteFile($this->metadataDir . $key);
                        }
                        return true;
                    };
                } elseif ($command === 'duplicate') {
                    $sourceKey = $getProperty('sourceKey', true);
                    $targetKey = $getProperty('targetKey', true);
                    $prepareFileForReading($this->objectsDir . $sourceKey, true);
                    $prepareFileForReading($this->metadataDir . $sourceKey);
                    $prepareFileForWriting($this->objectsDir . $targetKey);
                    $prepareFileForWriting($this->metadataDir . $targetKey);
                    $functions[$index] = function() use ($sourceKey, $targetKey, $getFileContent, $setFileContent, $deleteFile) {
                        $sourceBody = $getFileContent($this->objectsDir . $sourceKey);
                        if ($sourceBody === null) { // The source file is deleted in previous command
                            return false;
                        } else {
                            $sourceMetadata = $getFileContent($this->metadataDir . $sourceKey);
                            $setFileContent($this->objectsDir . $targetKey, $sourceBody);
                            if ($sourceMetadata === null) {
                                $deleteFile($this->metadataDir . $targetKey);
                            } else {
                                $setFileContent($this->metadataDir . $targetKey, $sourceMetadata);
                            }
                            return true;
                        }
                    };
                } elseif ($command === 'rename') {
                    $sourceKey = $getProperty('sourceKey', true);
                    $targetKey = $getProperty('targetKey', true);
                    $prepareFileForReading($this->objectsDir . $sourceKey, true);
                    $prepareFileForWriting($this->objectsDir . $sourceKey);
                    $prepareFileForWriting($this->metadataDir . $sourceKey);
                    $prepareFileForWriting($this->objectsDir . $targetKey);
                    $prepareFileForWriting($this->metadataDir . $targetKey);
                    $functions[$index] = function() use ($sourceKey, $targetKey, $getFileContent, $setFileContent, $deleteFile) {
                        $sourceBody = $getFileContent($this->objectsDir . $sourceKey);
                        if ($sourceBody === null) { // The source file is deleted in previous command
                            return false;
                        } else {
                            $sourceMetadata = $getFileContent($this->metadataDir . $sourceKey);
                            $setFileContent($this->objectsDir . $targetKey, $sourceBody);
                            if ($sourceMetadata === null) {
                                $deleteFile($this->metadataDir . $targetKey);
                            } else {
                                $setFileContent($this->metadataDir . $targetKey, $sourceMetadata);
                            }
                            $deleteFile($this->objectsDir . $sourceKey);
                            $deleteFile($this->metadataDir . $sourceKey);
                            return true;
                        }
                    };
                } elseif ($command === 'get') {
                    $key = $getProperty('key', true);
                    $resultKeys = $getProperty('result');
                    if ($resultKeys === null) {
                        $resultKeys = [];
                    }
                    $metadataResultKeys = $getProperty('result.metadata.*');
                    $returnBody = array_search('body', $resultKeys) !== false;
                    $returnMetadata = array_search('metadata', $resultKeys) !== false || !empty($metadataResultKeys);
                    if ($returnBody) {
                        $prepareFileForReading($this->objectsDir . $key);
                    }
                    if ($returnMetadata) {
                        $prepareFileForReading($this->metadataDir . $key);
                    }

                    $functions[$index] = function() use ($key, $resultKeys, $metadataResultKeys, $returnBody, $returnMetadata, $getFileContent, $decodeMetadata) {
                        $content = $getFileContent($this->objectsDir . $key);
                        if ($content === null) {
                            return [];
                        } else {
                            $objectResult = [];
                            if (array_search('key', $resultKeys) !== false) {
                                $objectResult['key'] = $key;
                            }
                            if ($returnBody) {
                                $objectResult['body'] = $content;
                            }
                            if ($returnMetadata) {
                                $objectMetadata = $decodeMetadata($getFileContent($this->metadataDir . $key));
                                if (array_search('metadata', $resultKeys) !== false) { // all metadata
                                    if (is_array($objectMetadata)) {
                                        foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                            $objectResult['metadata.' . $metadataKey] = $metadataValue;
                                        }
                                    }
                                } elseif (!empty($metadataResultKeys)) { // requested keys
                                    foreach ($metadataResultKeys as $metadataResultKey) {
                                        $objectResult['metadata.' . $metadataResultKey] = isset($objectMetadata[$metadataResultKey]) ? $objectMetadata[$metadataResultKey] : '';
                                    }
                                }
                            }
                            return $objectResult;
                        }
                    };
                } elseif ($command === 'search') {
                    $where = $getProperty('where');
                    if ($where === null) {
                        $where = [];
                    }
                    $resultKeys = $getProperty('result');
                    if ($resultKeys === null) {
                        $resultKeys = [];
                    }
                    $metadataResultKeys = $getProperty('result.metadata.*');
                    $returnBody = array_search('body', $resultKeys) !== false;
                    $returnMetadata = array_search('metadata', $resultKeys) !== false || !empty($metadataResultKeys);

                    $whereKeys = [];
                    $whereMetadataKeys = [];

                    $whereKeysPrepared = false;
                    if (isset($where['key'])) {
                        $whereKeysPrepared = true;
                        foreach ($where['key'] as $keyData) {
                            if ($keyData[0] === '==' || $keyData[0] === 'equal') {
                                $whereKeys[] = $keyData[1];
                            } elseif ($keyData[0] === 'startsWith' || $keyData[0] === 'startWith') {
                                $position = strrpos($keyData[1], '/');
                                if ($position !== false) {
                                    $dir = substr($keyData[1], 0, $position);
                                    $files = $this->getFiles($this->objectsDir . $dir . '/', true);
                                    foreach ($files as $filename) {
                                        $whereKeys[] = $dir . '/' . $filename;
                                    }
                                } else {
                                    $whereKeysPrepared = false;
                                    break;
                                }
                            } else {
                                $whereKeysPrepared = false;
                                break;
                            }
                        }
                    }
                    if (!$whereKeysPrepared) {
                        $whereKeys = $this->getFiles($this->objectsDir, true);
                    }
                    foreach ($where as $whereKey => $whereValue) {
                        if (substr($whereKey, 0, 9) === 'metadata.') {
                            $whereMetadataKeys[substr($whereKey, 9)] = $whereValue;
                        }
                    }

                    $hasWhereMetadata = !empty($whereMetadataKeys);
                    $hasWhereBody = isset($where['body']);

                    foreach ($whereKeys as $key) {
                        if ($returnBody || $hasWhereBody) {
                            $prepareFileForReading($this->objectsDir . $key);
                        }
                        if ($returnMetadata || $hasWhereMetadata) {
                            $prepareFileForReading($this->metadataDir . $key);
                        }
                    }

                    $functions[$index] = function() use ($where, $whereKeys, $resultKeys, $metadataResultKeys, $returnBody, $returnMetadata, $hasWhereBody, $hasWhereMetadata, $whereMetadataKeys, $getFileContent, $decodeMetadata, $areWhereConditionsMet) {
                        $result = [];
                        foreach ($whereKeys as $key) {

                            $objectResult = [];
                            if (isset($where['key'])) {
                                if (!$areWhereConditionsMet($key, $where['key'])) {
                                    continue;
                                }
                            }
                            $objectBody = null;
                            if ($returnBody || $hasWhereBody) {
                                $objectBody = $getFileContent($this->objectsDir . $key);
                                if ($objectBody === null) {
                                    continue;
                                }
                            }
                            if ($hasWhereBody && !$areWhereConditionsMet($objectBody, $where['body'])) {
                                continue;
                            }
                            $objectMetadata = $returnMetadata || $hasWhereMetadata ? $decodeMetadata($getFileContent($this->metadataDir . $key)) : [];
                            if ($hasWhereMetadata) {
                                $found = false;
                                foreach ($whereMetadataKeys as $whereMetadataKey => $whereMetadataValue) {
                                    if ($areWhereConditionsMet(isset($objectMetadata[$whereMetadataKey]) ? $objectMetadata[$whereMetadataKey] : '', $whereMetadataValue)) {
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) {
                                    continue;
                                }
                            }

                            if (array_search('key', $resultKeys) !== false) {
                                $objectResult['key'] = $key;
                            }
                            if ($returnBody) {
                                $objectResult['body'] = $objectBody;
                            }
                            if ($returnMetadata) {
                                if (array_search('metadata', $resultKeys) !== false) { // all metadata
                                    if (is_array($objectMetadata)) {
                                        foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                            $objectResult['metadata.' . $metadataKey] = $metadataValue;
                                        }
                                    }
                                } elseif (!empty($metadataResultKeys)) { // requested keys
                                    foreach ($metadataResultKeys as $metadataResultKey) {
                                        $objectResult['metadata.' . $metadataResultKey] = isset($objectMetadata[$metadataResultKey]) ? $objectMetadata[$metadataResultKey] : '';
                                    }
                                }
                            }

                            $result[] = $objectResult;
                        }
                        return $result;
                    };
                } else {
                    throw new \InvalidArgumentException('Invalid command "' . $command . '" at item[' . $index . ']');
                }
            }
        } catch (\Exception $e) {
            $thrownException = $e;
        }

        if ($thrownException === null) {
            $result = [];
            foreach ($commands as $index => $commandData) {
                $result[$index] = $functions[$index]();
            }
            unset($functions);
        }

        foreach ($filePointers as $filename => $filePointer) {
            flock($filePointer, LOCK_UN);
            fclose($filePointer);
        }
        unset($filePointers);

        foreach ($emptyOpenedFiles as $filename => $one) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }
        unset($emptyOpenedFiles);

        if ($thrownException === null) {
            foreach ($filesToDelete as $filename => $one) {
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
            unset($filesToDelete);
            restore_error_handler();
            return $result;
        } else {
            restore_error_handler();
            throw $thrownException;
        }
    }

    /**
     * Creates the directory of the file specified
     * 
     * @param string $filename The filename
     * @return boolean TRUE if successful, FALSE otherwise
     */
    private function createFileDirIfNotExists(string $filename): bool
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            if (is_dir($pathinfo['dirname'])) {
                return true;
            } elseif (is_file($pathinfo['dirname'])) {
                return false;
            } else {
                return $this->createDirIfNotExists($pathinfo['dirname']);
            }
        }
        return false;
    }

    /**
     * Creates a directory if not existent
     * 
     * @param string $dir The directory name
     */
    private function createDirIfNotExists(string $dir): bool
    {
        if (!is_dir($dir)) {
            try {
                set_error_handler(function($errno, $errstr, $errfile, $errline) {
                    restore_error_handler();
                    throw new \IvoPetkov\ObjectStorage\ErrorException($errstr, 0, $errno, $errfile, $errline);
                });
                $result = mkdir($dir, 0777, true);
                restore_error_handler();
                return (bool) $result;
            } catch (\IvoPetkov\ObjectStorage\ErrorException $e) {
                if ($e->getMessage() !== 'mkdir(): File exists') { // The directory may be just created in other process.
                    throw $e;
                }
            }
        }
        return true;
    }

    /**
     * Returns list of files in the directory specified
     * 
     * @param string $dir The directory name
     * @param boolean $recursive If TRUE all files in subdirectories will be returned too
     * @return array An array containing list of all files in the directory specified
     */
    private function getFiles(string $dir, bool $recursive = false): array
    {
        $result = [];
        if (is_dir($dir)) {
            $list = scandir($dir);
            if (is_array($list)) {
                foreach ($list as $filename) {
                    if ($filename != '.' && $filename != '..') {
                        if (is_dir($dir . $filename)) {
                            if ($recursive === true) {
                                $dirResult = $this->getFiles($dir . $filename . '/', true);
                                if (!empty($dirResult)) {
                                    foreach ($dirResult as $index => $value) {
                                        $dirResult[$index] = $filename . '/' . $value;
                                    }
                                    $result = array_merge($result, $dirResult);
                                }
                            }
                        } else {
                            $result[] = $filename;
                        }
                    }
                }
            }
        }
        return $result;
    }

}
