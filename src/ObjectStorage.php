<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 * Enables storing and manipulating data objects in the directory specified.
 */
class ObjectStorage
{

    /**
     * The directory where the objects will be stored.
     * 
     * @var string 
     */
    private $objectsDir = null;

    /**
     * The directory where the objects metadata will be stored.
     * 
     * @var string 
     */
    private $metadataDir = null;

    /**
     * Number of retries to make when waiting for locked (accessed by other scripts) objects.
     * 
     * @var int 
     */
    private $lockRetriesCount = 3;

    /**
     * Time (in microseconds) between retries when waiting for locked objects.
     * 
     * @var int 
     */
    private $lockRetryDelay = 500000;

    /**
     * Creates a new ObjectStorage instance.
     * 
     * @param string $objectsDir The directory where the library will store the objects.
     * @param string $metadataDir The directory where the library will store the objects metadata.
     * @param array $options List of options. Available values:
     * - lockRetriesCount - Number of retries to make when waiting for locked (accessed by other scripts) objects.
     * - lockRetryDelay - Time (in microseconds) between retries when waiting for locked objects.
     * @throws \InvalidArgumentException
     */
    public function __construct(string $objectsDir, string $metadataDir, array $options = [])
    {
        $this->objectsDir = rtrim($objectsDir, '/\\') . '/';
        $this->metadataDir = rtrim($metadataDir, '/\\') . '/';

        if (isset($options['lockRetriesCount'])) {
            if (!is_int($options['lockRetriesCount'])) {
                throw new \InvalidArgumentException('The lockRetriesCount option must be of type int!');
            }
            $this->lockRetriesCount = $options['lockRetriesCount'];
        }

        if (isset($options['lockRetryDelay'])) {
            if (!is_int($options['lockRetryDelay'])) {
                throw new \InvalidArgumentException('The lockRetryDelay option must be of type int!');
            }
            $this->lockRetryDelay = $options['lockRetryDelay'];
        }
    }

    /**
     * Retrieves object data for the specified key.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'result' => ['body', 'body.length', 'body.range(*,*)', 'metadata.year']]
     * @return array|null An array containing the result data if existent, NULL otherwise.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function get(array $parameters): ?array
    {
        return $this->executeCommand([$parameters], 'get')[0];
    }

    /**
     * Checks if the specified object exists.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1']
     * @return bool Returns TRUE if the object exists, FALSE otherwise.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function exists(array $parameters): bool
    {
        return $this->executeCommand([$parameters], 'exists')[0];
    }

    /**
     * Saves object data for a specified key.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1', 'metadata.year' => '2000']. Specifying metadata.* will bulk remove/update all previous metadata.
     * @return void No value is returned.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function set(array $parameters): void
    {
        $this->executeCommand([$parameters], 'set')[0];
    }

    /**
     * Appends object data for a specified key. The object will be created if not existent.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1']
     * @return void No value is returned.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function append(array $parameters): void
    {
        $this->executeCommand([$parameters], 'append')[0];
    }

    /**
     * Creates a copy of an object. It's metadata is copied too.
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return void No value is returned.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     * @throws \IvoPetkov\ObjectStorage\ObjectNotFoundException
     */
    public function duplicate(array $parameters): void
    {
        $this->executeCommand([$parameters], 'duplicate')[0];
    }

    /**
     * Renames an object.
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return void No value is returned.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     * @throws \IvoPetkov\ObjectStorage\ObjectNotFoundException
     */
    public function rename(array $parameters): void
    {
        $this->executeCommand([$parameters], 'rename')[0];
    }

    /**
     * Deletes an object and it's metadata.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1']
     * @return void No value is returned.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function delete(array $parameters): void
    {
        $this->executeCommand([$parameters], 'delete')[0];
    }

    /**
     * Retrieves a list of all object matching the criteria specified.
     * 
     * @param array $parameters Data in the following format:
     *    // Finds objects by key 
     *    [
     *        'where' => [
     *            ['key', ['book-1449392776', 'book-1430268158']]
     *        ],
     *        'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
     *    ]
     *    // Finds objects by metadata 
     *    [
     *        'where' => [
     *            ['metadata.year', '2013']
     *        ],
     *        'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
     *    ]
     *    // Finds objects by regular expression 
     *    [
     *        'where' => [
     *            ['key', '^prefix1\/', 'regExp']
     *        ],
     *        'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
     *    ]
     * @return array An array containing all matching objects.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function search(array $parameters): array
    {
        return $this->executeCommand([$parameters], 'search')[0];
    }

    /**
     * Executes single command.
     * 
     * @param array $parameters The command parameters.
     * @param string $command The command name.
     * @return mixed
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
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
     * Checks whether the key specified is valid.
     * 
     * @param string $key The key to check.
     * @return boolean TRUE if the key is valid, FALSE otherwise.
     */
    public function validate($key): bool
    {
        if (!is_string($key) || strlen($key) === 0 || $key === '.' || $key === '..' || strpos($key, '/../') !== false || strpos($key, '/./') !== false || strpos($key, '/') === 0 || strpos($key, './') === 0 || strpos($key, '../') === 0 || substr($key, -2) === '/.' || substr($key, -3) === '/..' || substr($key, -1) === '/') {
            return false;
        }
        return preg_match("/^[a-z0-9\.\/\-\_]*$/", $key) === 1;
    }

    /**
     * Executes list of commands.
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
     * @return array Array containing the results for the commands.
     * @throws \InvalidArgumentException
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     * @throws \IvoPetkov\ObjectStorage\ObjectLockedException
     */
    public function execute(array $commands): array
    {
        $filePointers = [];
        $filesToDelete = [];
        $emptyOpenedFiles = []; // opened but no content set

        $logStorageAccess = isset($this->internalStorageAccessLog);

        $encodeMetaData = function ($metadata) {
            return "content-type:json\n\n" . json_encode($metadata);
        };

        $decodeMetadata = function ($metadata) {
            if (!isset($metadata[0])) {
                return [];
            }
            $parts = explode("\n\n", $metadata, 2);
            if (!isset($parts[1]) || $parts[0] !== 'content-type:json') {
                return [];
            }
            $result = json_decode($parts[1], true);
            return is_array($result) ? $result : [];
        };

        $isValidMetadata = function ($key, $value, $allowWildcard) {
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

        $areWhereConditionsMet = function ($value, $conditions) {
            foreach ($conditions as $conditionData) {
                if ($conditionData[0] === 'equal') {
                    if ($value === $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'notEqual') {
                    if ($value !== $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'regExp') {
                    if (preg_match('/' . $conditionData[1] . '/', $value) === 1) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'notRegExp') {
                    if (preg_match('/' . $conditionData[1] . '/', $value) === 0) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'startWith') {
                    if (substr($value, 0, strlen($conditionData[1])) === $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'notStartWith') {
                    if (substr($value, 0, strlen($conditionData[1])) !== $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'endWith') {
                    if (substr($value, -strlen($conditionData[1])) === $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'notEndWith') {
                    if (substr($value, -strlen($conditionData[1])) !== $conditionData[1]) {
                        continue;
                    }
                    return false;
                } elseif ($conditionData[0] === 'contain') {
                    if (strpos($value, $conditionData[1]) !== false) {
                        continue;
                    }
                    return false;
                }
            }
            return true;
        };

        $prepareFileForWriting = function ($filename) use (&$filePointers, &$emptyOpenedFiles, $logStorageAccess) {
            if (isset($filePointers[$filename])) {
                return;
            }
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for writing.'];
            }
            if (is_dir($filename)) {
                throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not writable (dir with the same name exists).');
            }
            if ($this->createFileDirIfNotExists($filename) === false) {
                throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not writable (cannot create dir).');
            }
            $getFilePointer = function () use ($filename, &$emptyOpenedFiles, $logStorageAccess) {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['clearstatcache', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for writing.'];
                }
                clearstatcache(false, $filename);
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for writing.'];
                }
                $isNew = !is_file($filename);
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['fopen', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for writing.'];
                }
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

        $prepareFileForReading = function ($filename, $required = false) use (&$filePointers, $logStorageAccess) {
            if (isset($filePointers[$filename])) {
                return true;
            }
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for reading.'];
            }
            if (is_file($filename)) {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_readable', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Prepare for reading.'];
                }
                if (!is_readable($filename)) {
                    throw new \IvoPetkov\ObjectStorage\ErrorException('The file ' . $filename . ' is not readable.');
                }
            } else {
                if ($required) {
                    throw new \IvoPetkov\ObjectStorage\ObjectNotFoundException('The file ' . $filename . ' does not exist.');
                }
                $rootDir = dirname($filename, 100);
                $isParentDirReadable = false;
                for ($i = 1; $i < 100; $i++) {
                    $dirToCheck = dirname($filename, $i);
                    if ($logStorageAccess) {
                        $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dirToCheck), 'Prepare dir for reading.'];
                        $this->internalStorageAccessLog[] = ['is_readable', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dirToCheck), 'Prepare dir for reading.'];
                    }
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

        $setFileContent = function ($filename, $content) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
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

        $appendFileContent = function ($filename, $content) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
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

        $getFileContent = function ($filename, $rangeStart = null, $rangeEnd = null) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles, $logStorageAccess) {
            if (isset($filesToDelete[$filename])) {
                return null;
            }
            if (isset($emptyOpenedFiles[$filename])) {
                return null;
            }
            $getContent = function ($filePointer, $start, $end) {
                $content = '';
                if ($start !== null) {
                    fseek($filePointer, $start);
                    if ($end !== null) {
                        $maxLength = $end !== null ? $end - $start : null;
                        $contentLength = 0;
                        for ($length = 8192; $length <= $maxLength; $length += 8192) {
                            $content .= fread($filePointer, $length);
                            $contentLength = $length;
                        }
                        if ($contentLength < $maxLength) {
                            $content .= fread($filePointer, $maxLength - $contentLength);
                        }
                        return $content;
                    }
                } else {
                    fseek($filePointer, 0);
                }
                while (!feof($filePointer)) {
                    $content .= fread($filePointer, 8192);
                }
                return $content;
            };
            if (isset($filePointers[$filename])) {
                $filePointer = $filePointers[$filename];
                $pointerPosition = ftell($filePointer); // save the current pointer position
                $content = $getContent($filePointer, $rangeStart, $rangeEnd);
                fseek($filePointer, $pointerPosition); // restore pointer position
                return $content;
            } else {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Get file content.'];
                }
                if (is_file($filename)) {
                    if ($logStorageAccess) {
                        $this->internalStorageAccessLog[] = ['fopen', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Get file content.'];
                    }
                    $filePointer = fopen($filename, "r");
                    flock($filePointer, LOCK_SH);
                    $content = $getContent($filePointer, $rangeStart, $rangeEnd);
                    flock($filePointer, LOCK_UN);
                    fclose($filePointer);
                    return $content;
                }
                return null;
            }
        };

        $getFileSize = function ($filename) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles, $logStorageAccess) {
            if (isset($filesToDelete[$filename])) {
                return null;
            }
            if (isset($emptyOpenedFiles[$filename])) {
                return null;
            }
            if (isset($filePointers[$filename])) {
                $filePointer = $filePointers[$filename];
                $pointerPosition = ftell($filePointer);
                fseek($filePointer, 0, SEEK_END);
                $size = ftell($filePointer);
                fseek($filePointer, $pointerPosition);
                return $size;
            } else {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Get file size.'];
                }
                if (is_file($filename)) {
                    if ($logStorageAccess) {
                        $this->internalStorageAccessLog[] = ['filesize', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Get file size.'];
                    }
                    return filesize($filename);
                }
                return null;
            }
        };

        $fileExists = function ($filename) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles, $logStorageAccess) {
            if (isset($filesToDelete[$filename])) {
                return false;
            }
            if (isset($emptyOpenedFiles[$filename])) {
                return false;
            }
            if (isset($filePointers[$filename])) {
                return true;
            } else {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'File exists.'];
                }
                return is_file($filename);
            }
        };

        $deleteFile = function ($filename) use (&$filePointers, &$filesToDelete, &$emptyOpenedFiles) {
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

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            restore_error_handler();
            throw new \IvoPetkov\ObjectStorage\ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        $functions = [];
        $thrownException = null;
        try {
            foreach ($commands as $index => $commandData) {

                $getProperty = function ($name, $required = false) use ($index, $commandData, $isValidMetadata) {
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
                            if (!$this->validate($commandData['key'])) {
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
                            if (!$this->validate($commandData['sourceKey'])) {
                                throw new \InvalidArgumentException('The sourceKey property is not valid for item[' . $index . ']');
                            }
                            return $commandData['sourceKey'];
                        }
                    } elseif ($name === 'targetKey') {
                        if (isset($commandData['targetKey'])) {
                            if (!is_string($commandData['targetKey'])) {
                                throw new \InvalidArgumentException('The targetKey property must be of type string for item[' . $index . ']');
                            }
                            if (!$this->validate($commandData['targetKey'])) {
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
                                    $whereOperator = isset($whereItem[2]) ? $whereItem[2] : 'equal';
                                    if (array_search($whereOperator, ['equal', 'notEqual', 'regExp', 'notRegExp', 'startWith', 'notStartWith', 'endWith', 'notEndWith', 'contain']) === false) {
                                        throw new \InvalidArgumentException('Invalid where operator ' . $whereOperator . '.');
                                    }
                                    if (is_string($whereValue)) {
                                        if (!isset($whereValue[0])) {
                                            throw new \InvalidArgumentException('The operator ' . $whereOperator . ' value cannot be empty!');
                                        }
                                        $result[$whereKey][] = [$whereOperator, $whereValue];
                                        $valid = true;
                                    }
                                    // removed support for multiple values
                                    //  elseif (is_array($whereValue)) {
                                    //     $valid = true;
                                    //     foreach ($whereValue as $whereValueItem) {
                                    //         if (!is_string($whereValueItem)) {
                                    //             $valid = false;
                                    //             break;
                                    //         }
                                    //     }
                                    //     if ($valid) {
                                    //         $result[$whereKey] = [$whereOperator, array_unique($whereValue)];
                                    //     }
                                    // }
                                }
                                if (!$valid) {
                                    throw new \InvalidArgumentException('Where data not valid.');
                                }
                            }

                            if (isset($result['key'])) {
                                foreach ($result['key'] as $whereKeyData) {
                                    $whereOperator = $whereKeyData[0];
                                    if ($whereOperator === 'equal') {
                                        if (!$this->validate($whereKeyData[1])) {
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
                    } elseif ($name === 'result.body.range.*') {
                        if (isset($commandData['result'])) {
                            if (!is_array($commandData['result'])) {
                                throw new \InvalidArgumentException('The result property must be of type array for item[' . $index . ']');
                            }
                            $resultKeys = isset($commandData['result']) ? $commandData['result'] : [];
                            $result = [];
                            foreach ($resultKeys as $resultCode) {
                                if (substr($resultCode, 0, 10) === 'body.range') {
                                    $rangeValue = substr($resultCode, 10);
                                    $matches = null;
                                    if (preg_match('/\(([0-9]*),([0-9]*)\)/', $rangeValue, $matches) === 1) {
                                        $result[] = [$rangeValue, $matches[1], $matches[2]];
                                    } elseif (preg_match('/\(([0-9]*)\)/', $rangeValue, $matches) === 1) {
                                        $result[] = [$rangeValue, $matches[1], null];
                                    } else {
                                        throw new \InvalidArgumentException('The range value (' . $resultCode . ') is not valid for item[' . $index . ']');
                                    }
                                }
                            }
                            if (!empty($result)) {
                                return $result;
                            }
                        }
                    } elseif ($name === 'limit') {
                        if (isset($commandData['limit'])) {
                            if (!is_int($commandData['limit'])) {
                                throw new \InvalidArgumentException('The limit property must be of type int for item[' . $index . ']');
                            }
                            return $commandData['limit'];
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

                    $functions[$index] = function () use ($key, $body, $metadata, $modifyBody, $modifyMetadata, $setFileContent, $getFileContent, $deleteFile, $decodeMetadata, $encodeMetaData) {
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
                    };
                } elseif ($command === 'append') {
                    $key = $getProperty('key', true);
                    $body = $getProperty('body', true);
                    $prepareFileForWriting($this->objectsDir . $key);
                    $functions[$index] = function () use ($key, $body, $appendFileContent) {
                        $appendFileContent($this->objectsDir . $key, $body);
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
                    $functions[$index] = function () use ($key, $deleteObjectFile, $deleteMetadataFile, $deleteFile) {
                        if ($deleteObjectFile) {
                            $deleteFile($this->objectsDir . $key);
                        }
                        if ($deleteMetadataFile) {
                            $deleteFile($this->metadataDir . $key);
                        }
                    };
                } elseif ($command === 'duplicate') {
                    $sourceKey = $getProperty('sourceKey', true);
                    $targetKey = $getProperty('targetKey', true);
                    $prepareFileForReading($this->objectsDir . $sourceKey, true);
                    $prepareFileForReading($this->metadataDir . $sourceKey);
                    $prepareFileForWriting($this->objectsDir . $targetKey);
                    $prepareFileForWriting($this->metadataDir . $targetKey);
                    $functions[$index] = function () use ($sourceKey, $targetKey, $getFileContent, $setFileContent, $deleteFile) {
                        $sourceBody = $getFileContent($this->objectsDir . $sourceKey);
                        if ($sourceBody === null) { // The source file is deleted in previous command
                            throw new \IvoPetkov\ObjectStorage\ObjectNotFoundException('The source object (' . $sourceKey . ') does not exists!');
                        } else {
                            $sourceMetadata = $getFileContent($this->metadataDir . $sourceKey);
                            $setFileContent($this->objectsDir . $targetKey, $sourceBody);
                            if ($sourceMetadata === null) {
                                $deleteFile($this->metadataDir . $targetKey);
                            } else {
                                $setFileContent($this->metadataDir . $targetKey, $sourceMetadata);
                            }
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
                    $functions[$index] = function () use ($sourceKey, $targetKey, $getFileContent, $setFileContent, $deleteFile) {
                        $sourceBody = $getFileContent($this->objectsDir . $sourceKey);
                        if ($sourceBody === null) { // The source file is deleted in previous command
                            throw new \IvoPetkov\ObjectStorage\ObjectNotFoundException('The source object (' . $sourceKey . ') does not exists!');
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
                        }
                    };
                } elseif ($command === 'get') {
                    $key = $getProperty('key', true);
                    $resultKeys = $getProperty('result');
                    if ($resultKeys === null) {
                        $resultKeys = [];
                    }
                    $metadataResultKeys = $getProperty('result.metadata.*');
                    $returnMetadata = array_search('metadata', $resultKeys) !== false || !empty($metadataResultKeys);
                    $returnBody = array_search('body', $resultKeys) !== false;
                    $returnBodyLength = array_search('body.length', $resultKeys) !== false;
                    $bodyRangeResultKeys = $getProperty('result.body.range.*');
                    $returnBodyRanges = !empty($bodyRangeResultKeys);
                    if ($returnBody || $returnBodyRanges) {
                        $prepareFileForReading($this->objectsDir . $key);
                    }
                    if ($returnMetadata) {
                        $prepareFileForReading($this->metadataDir . $key);
                    }

                    $functions[$index] = function () use ($key, $resultKeys, $metadataResultKeys, $returnBody, $returnBodyLength, $returnMetadata, $returnBodyRanges, $bodyRangeResultKeys, $getFileContent, $getFileSize, $fileExists, $decodeMetadata) {
                        if ($returnBody) {
                            $content = $getFileContent($this->objectsDir . $key);
                            if ($content === null) {
                                return;
                            }
                            if ($returnBodyLength) {
                                $size = strlen($content);
                            }
                        } elseif ($returnBodyRanges) {
                            if ($returnBodyLength) {
                                $size = $getFileSize($this->objectsDir . $key);
                            }
                        } elseif ($returnBodyLength) {
                            if (!$fileExists($this->objectsDir . $key)) {
                                return;
                            }
                            $size = $getFileSize($this->objectsDir . $key);
                        } else {
                            if (!$fileExists($this->objectsDir . $key)) {
                                return;
                            }
                        }
                        $objectResult = [];
                        if (array_search('key', $resultKeys) !== false) {
                            $objectResult['key'] = $key;
                        }
                        if ($returnBody) {
                            $objectResult['body'] = $content;
                        }
                        if ($returnBodyLength) {
                            $objectResult['body.length'] = $size;
                        }
                        if ($returnBodyRanges) {
                            foreach ($bodyRangeResultKeys as $range) {
                                $start = $range[1];
                                $end = $range[2];
                                $objectResult['body.range' . $range[0]] = $returnBody ? ($end !== null ? substr($content, $start, $end - $start) : substr($content, $start)) : $getFileContent($this->objectsDir . $key, $start, $end);
                            }
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
                    };
                } elseif ($command === 'exists') {
                    $key = $getProperty('key', true);
                    $functions[$index] = function () use ($key, $fileExists) {
                        return $fileExists($this->objectsDir . $key);
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
                    $limit = $getProperty('limit');
                    $metadataResultKeys = $getProperty('result.metadata.*');
                    $returnMetadata = array_search('metadata', $resultKeys) !== false || !empty($metadataResultKeys);
                    $returnBody = array_search('body', $resultKeys) !== false;
                    $returnBodyLength = array_search('body.length', $resultKeys) !== false;
                    $bodyRangeResultKeys = $getProperty('result.body.range.*');
                    $returnBodyRanges = !empty($bodyRangeResultKeys);

                    $whereKeys = [];
                    $whereMetadataKeys = [];

                    $getFilesOptions = [
                        'equal' => [],
                        'notEqual' => [],
                        'startWith' => [],
                        'notStartWith' => [],
                    ];

                    if (isset($where['key'])) {
                        $temp = [];
                        foreach ($where['key'] as $keyData) {
                            if ($keyData[0] === 'equal') {
                                $getFilesOptions['equal'][] = $keyData[1];
                            } elseif ($keyData[0] === 'startWith') {
                                $getFilesOptions['startWith'][] = $keyData[1];
                            } elseif ($keyData[0] === 'notEqual') {
                                $getFilesOptions['notEqual'][] = $keyData[1];
                            } elseif ($keyData[0] === 'notStartWith') {
                                $getFilesOptions['notStartWith'][] = $keyData[1];
                            } else {
                                $temp[] = $keyData;
                            }
                        }
                        if (empty($temp)) {
                            unset($where['key']);
                        } else {
                            $where['key'] = $temp;
                        }
                        unset($temp);
                    }

                    $whereKeys = $this->getFiles($this->objectsDir, true, $limit, $getFilesOptions);

                    foreach ($where as $whereKey => $whereValue) {
                        if (substr($whereKey, 0, 9) === 'metadata.') {
                            $whereMetadataKeys[substr($whereKey, 9)] = $whereValue;
                        }
                    }

                    $hasWhereMetadata = !empty($whereMetadataKeys);
                    $hasWhereBody = isset($where['body']);

                    foreach ($whereKeys as $key) {
                        if ($returnBody || $returnBodyRanges || $hasWhereBody) {
                            $prepareFileForReading($this->objectsDir . $key);
                        }
                        if ($returnMetadata || $hasWhereMetadata) {
                            $prepareFileForReading($this->metadataDir . $key);
                        }
                    }

                    $functions[$index] = function () use ($where, $whereKeys, $resultKeys, $metadataResultKeys, $returnBody, $returnBodyRanges, $bodyRangeResultKeys, $returnBodyLength, $returnMetadata, $hasWhereBody, $hasWhereMetadata, $whereMetadataKeys, $getFileContent, $getFileSize, $decodeMetadata, $areWhereConditionsMet) {
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
                            if ($returnBodyLength) {
                                $objectResult['body.length'] = $returnBody ? strlen($objectBody) : $getFileSize($this->objectsDir . $key);
                            }
                            if ($returnBodyRanges) {
                                foreach ($bodyRangeResultKeys as $range) {
                                    $start = $range[1];
                                    $end = $range[2];
                                    $objectResult['body.range' . $range[0]] = $returnBody || $hasWhereBody ? ($end !== null ? substr($objectBody, $start, $end - $start) : substr($objectBody, $start)) : $getFileContent($this->objectsDir . $key, $start, $end);
                                }
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
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Remove empty files.'];
            }
            if (is_file($filename)) {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['unlink', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Remove empty files.'];
                }
                unlink($filename);
            }
        }
        unset($emptyOpenedFiles);

        if ($thrownException === null) {
            foreach ($filesToDelete as $filename => $one) {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Remove deleted files.'];
                }
                if (is_file($filename)) {
                    if ($logStorageAccess) {
                        $this->internalStorageAccessLog[] = ['unlink', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $filename), 'Remove deleted files.'];
                    }
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
     * Creates the directory of the file specified.
     * 
     * @param string $filename The filename.
     * @return boolean TRUE if successful, FALSE otherwise.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     */
    private function createFileDirIfNotExists(string $filename): bool
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            $logStorageAccess = isset($this->internalStorageAccessLog);
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $pathinfo['dirname']), 'Create file dir.'];
            }
            if (is_dir($pathinfo['dirname'])) {
                return true;
            }
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $pathinfo['dirname']), 'Create file dir.'];
            }
            if (is_file($pathinfo['dirname'])) {
                return false;
            }
            return $this->createDirIfNotExists($pathinfo['dirname']);
        }
        return false;
    }

    /**
     * Creates a directory if not existent.
     * 
     * @param string $dir The directory name.
     * @return boolean TRUE if successful, FALSE otherwise.
     * @throws \IvoPetkov\ObjectStorage\ErrorException
     */
    private function createDirIfNotExists(string $dir): bool
    {
        $logStorageAccess = isset($this->internalStorageAccessLog);
        if ($logStorageAccess) {
            $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir), 'Create dir.'];
        }
        if (!is_dir($dir)) {
            try {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                    restore_error_handler();
                    throw new \IvoPetkov\ObjectStorage\ErrorException($errstr, 0, $errno, $errfile, $errline);
                });
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['mkdir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir), 'Create dir.'];
                }
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
     * Returns list of files in the directory specified.
     * 
     * @param string $dir The directory name.
     * @param boolean $recursive If TRUE all files in subdirectories will be returned too.
     * @param int|null $limit Maximum number of files to return.
     * @param array $options Available values: equal, notEqual, startWith, notStartWith
     * @return array An array containing list of all files in the directory specified.
     */
    private function getFiles(string $dir, bool $recursive = false, $limit = null, array $options = []): array
    {
        $logStorageAccess = isset($this->internalStorageAccessLog);
        $keyPrefix = '';
        $equal = $options['equal'];
        if (!empty($equal)) {
            $equal = array_unique($equal);
            if (sizeof($equal) > 1) { // impossible case
                return [];
            }
            $equal = array_values($equal);
        }
        $startWith = $options['startWith'];
        if (!empty($startWith)) {
            $startWith = array_values(array_unique($startWith));
            $isLessSpecific = function ($index, $prefix) use ($startWith) {
                foreach ($startWith as $_index => $_prefix) {
                    if ($_index !== $index) {
                        if (strpos($prefix, $_prefix) === 0) {
                            return true;
                        }
                    }
                }
                return false;
            };
            $lessSpecific = [];
            foreach ($startWith as $index => $prefix) {
                if ($isLessSpecific($index, $prefix)) {
                    $lessSpecific[] = $prefix;
                }
            }
            $startWith = array_diff($startWith, $lessSpecific);
            if (sizeof($startWith) > 1) { // impossible case
                return [];
            }
            $startWith = array_values($startWith);
        }
        $notEqual = $options['notEqual'];
        $notStartWith = $options['notStartWith'];
        if (isset($equal[0])) {
            $filename = $equal[0];
            if (isset($startWith[0])) {
                if (strpos($filename, $startWith[0]) !== 0) {
                    return [];
                }
            }
            foreach ($notEqual as $_notEqual) {
                if ($filename === $_notEqual) {
                    return [];
                }
            }
            foreach ($notStartWith as $_notStartWith) {
                if (strpos($filename, $_notStartWith) === 0) {
                    return [];
                }
            }
            if ($logStorageAccess) {
                $this->internalStorageAccessLog[] = ['is_file', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir . $filename), 'Get files list.'];
            }
            if (is_file($dir . $filename)) {
                return [$filename];
            } else {
                return [];
            }
        }
        if (isset($startWith[0])) {
            foreach ($notStartWith as $_notStartWith) {
                if (strpos($startWith[0], $_notStartWith) === 0) { // confict with notStartWith
                    return [];
                }
            }
            $separatorIndex = strrpos($startWith[0], '/'); // optimize dir access (start with a child dir if found in the key)
            if ($separatorIndex !== false) {
                if ($separatorIndex === 0) { // cannot start with /
                    return [];
                }
                $keyPrefix = substr($startWith[0], 0, $separatorIndex) . '/';
                $dir .= $keyPrefix;
            }
            if (!empty($notStartWith)) { // remove not needed prefixes (outside the startWith)
                $temp = [];
                foreach ($notStartWith as $_notStartWith) {
                    if (strpos($_notStartWith, $startWith[0]) === 0) {
                        $temp[] = $_notStartWith;
                    }
                }
                $notStartWith = $temp;
            }
        }

        $buildPrefixesIndex = function (array $prefixes) {
            $getIndex = function (array $prefixes) use (&$getIndex) {
                $index = [];
                foreach ($prefixes as $prefix) {
                    $length = $prefix[0] + 1;
                    $start = substr($prefix[1], 0, $length);
                    if (!isset($index[$start])) {
                        $index[$start] = [];
                    }
                    $index[$start][] = [$length, $prefix[1]];
                }
                $keysToRemove = [];
                $keysToAdd = [];
                foreach ($index as $k => $indexPrefixes) {
                    if (sizeof($indexPrefixes) === 1) {
                        $keysToRemove[] = $k;
                        $keysToAdd[$indexPrefixes[0][1]] = true;
                    } else {
                        $index[$k] = $getIndex($indexPrefixes);
                        if (sizeof($index[$k]) === 1) {
                            $keysToRemove[] = $k;
                            $keysToAdd[key($index[$k])] = current($index[$k]);
                        }
                    }
                }
                foreach ($keysToRemove as $keyToRemove) {
                    unset($index[$keyToRemove]);
                }
                foreach ($keysToAdd as $keyToAdd => $value) {
                    $index[$keyToAdd] = $value;
                }
                return $index;
            };
            foreach ($prefixes as $i => $prefix) {
                $prefixes[$i] = [0, $prefix];
            }
            return $getIndex($prefixes);
        };

        $existsInPrefixesIndex = function (string $prefix, array $index) use (&$existsInPrefixesIndex) {
            foreach ($index as $key => $value) {
                if (strpos($prefix, $key) === 0) {
                    if ($value === true) {
                        return true;
                    } else {
                        if ($existsInPrefixesIndex($prefix, $value)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        };

        $notStartWithPrefixesIndex = empty($notStartWith) ? null : $buildPrefixesIndex($notStartWith);

        $getFiles = function (string $dir, bool $checkDir, bool $recursive, $limit, string $keyPrefix) use (&$getFiles, $logStorageAccess, $notEqual, $startWith, $notStartWithPrefixesIndex, &$existsInPrefixesIndex) {
            if ($limit === 0) {
                return [];
            }
            $result = [];
            if ($logStorageAccess) {
                if ($checkDir) {
                    $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir), 'Get files list.'];
                }
            }
            if (!$checkDir || is_dir($dir)) {
                if ($logStorageAccess) {
                    $this->internalStorageAccessLog[] = ['scandir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir), 'Get files list.'];
                }
                $list = scandir($dir);
                if (is_array($list)) {
                    foreach ($list as $filename) {
                        if ($filename != '.' && $filename != '..') {
                            if (isset($startWith[0])) { // can be only one
                                if (strpos($keyPrefix . $filename, $startWith[0]) !== 0) {
                                    continue;
                                }
                            }
                            if ($notStartWithPrefixesIndex !== null) {
                                if ($existsInPrefixesIndex($keyPrefix . $filename, $notStartWithPrefixesIndex)) {
                                    continue;
                                }
                            }
                            if ($logStorageAccess) {
                                $this->internalStorageAccessLog[] = ['is_dir', str_replace([$this->objectsDir, $this->metadataDir], ['OBJECTSDIR/', 'METADATADIR/'], $dir . $filename), 'Get files list.'];
                            }
                            if (is_dir($dir . $filename)) {
                                if ($recursive === true) {
                                    $result = array_merge($result, $getFiles($dir . $filename . '/', false, true, $limit !== null ? ($limit - sizeof($result)) : null, $keyPrefix . $filename . '/'));
                                }
                            } else {
                                $continue = false;
                                foreach ($notEqual as $_notEqual) {
                                    if ($keyPrefix . $filename === $_notEqual) {
                                        $continue = true;
                                        break;
                                    }
                                }
                                if ($continue) {
                                    continue;
                                }
                                $result[] = $keyPrefix . $filename;
                                if ($limit !== null && $limit === sizeof($result)) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            return $result;
        };
        return $getFiles($dir, true, $recursive, $limit, $keyPrefix);
    }
}
