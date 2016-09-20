<?php

/*
 * Object Storage
 * https://github.com/ivopetkov/object-storage
 * Copyright (c) 2016 Ivo Petkov
 * Free to use under the MIT license.
 */

class ObjectStorage
{

    /**
     * The current library version
     */
    const VERSION = '0.1.2';

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
     * @throws \InvalidArgumentException
     */

    public function __construct($dir = 'data/')
    {
        if (!is_string($dir)) {
            throw new \InvalidArgumentException('The dir argument must be a string');
        }
        $dir = rtrim($dir, '/\\') . '/';
        $this->objectsDir = $dir . 'objects/';
        $this->metadataDir = $dir . 'metadata/';
        $this->tempDir = $dir . 'temp/';
    }

    /**
     * Retrieves object data for a specified key
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'result' => ['body', 'metadata.year']]
     * @return array An array containing the result data if existent, empty array otherwise
     */
    public function get($parameters)
    {
        return $this->executeCommand([$parameters], 'get')[0];
    }

    /**
     * 
     * @param array $parameters
     * @return boolean
     */
//    function add($parameters)
//    {
//        return $this->executeCommand([$parameters], 'add')[0];
//    }

    /**
     * Saves object data for a specified key
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1', 'metadata.year' => '2000']
     * @return boolean TRUE if successful, FALSE otherwise
     */
    public function set($parameters)
    {
        return $this->executeCommand([$parameters], 'set')[0];
    }

    /**
     * Appends object data for a specified key. The object will be created if not existent.
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1', 'body' => 'body1']
     * @return boolean
     */
    public function append($parameters)
    {
        return $this->executeCommand([$parameters], 'append')[0];
    }

    /**
     * Creates a copy of an object. It's metadata is copied too.
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return boolean
     */
    public function duplicate($parameters)
    {
        return $this->executeCommand([$parameters], 'duplicate')[0];
    }

    /**
     * Renames an object
     * 
     * @param array $parameters Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']
     * @return boolean
     */
    public function rename($parameters)
    {
        return $this->executeCommand([$parameters], 'rename')[0];
    }

    /**
     * Deletes an object and it's metadata
     * 
     * @param array $parameters Data in the following format: ['key' => 'example1']
     * @return boolean
     */
    public function delete($parameters)
    {
        return $this->executeCommand([$parameters], 'delete')[0];
    }

    /**
     * Retrieves a list of all object matching the criteria specified
     * 
     * @param array $parameters Data in the following format:
     *    // Finds objects by key 
     *    [
     *        'where' => [
     *            ['key', ['book-1449392776', 'book-1430268158']]],
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     *    // Finds objects by metadata 
     *    [
     *        'where' => [
     *            ['metadata.year', '2013']
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     *    // Finds objects by regular expression 
     *    [
     *        'where' => [
     *            ['key', '^prefix1\/', 'regexp']
     *        'result' => ['key', 'body', 'metadata.title']
     *    ]
     * @return array
     */
    public function search($parameters)
    {
        return $this->executeCommand([$parameters], 'search')[0];
    }

//    function getObjectBody($key)
//    {
//        $parameters = [
//            [
//                'where' => [
//                    ['key', $key]
//                ],
//                'result' => ['key', 'body']
//            ]
//        ];
//        $result = $this->executeCommand($parameters, 'search');
//        return isset($result[0], $result[0][0]) ? $result[0][0]['body'] : false;
//    }
//
//    function objectExists($key)
//    {
//        $parameters = [
//            [
//                'where' => [
//                    ['key', $key]
//                ]
//            ]
//        ];
//        $result = $this->executeCommand($parameters, 'search');
//        return isset($result[0], $result[0][0]);
//    }

    /**
     * Executes single command
     * 
     * @param array $parameters The command parameters
     * @param string $command The command name
     * @return mixed
     */
    private function executeCommand($parameters, $command)
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
        return $result;
    }

    /**
     * Returns file pointer for writing retrying several times. Returns false if unsuccessful.
     * 
     * @param string $filename The filename
     * @return resource|false File pointer of false if unsuccessful
     * @throws \ObjectStorage\ErrorException
     */
    private function getFilePointerForWriting($filename)
    {
        if ($this->createFileDirIfNotExists($filename) === false) {
            throw new \ObjectStorage\ErrorException('Cannot write at ' . $filename);
        }
        for ($i = 0; $i < $this->lockRetriesCount; $i++) {
            $filePointer = $this->tryGetFilePointerForWriting($filename);
            if ($filePointer !== false) {
                return $filePointer;
            }
            if ($i < $this->lockRetriesCount - 1) {
                usleep($this->lockRetryDelay);
            }
        }
        return false;
    }

    /**
     * Returns file pointer for writing or false
     * 
     * @param string $filename The filename
     * @return resource|false File pointer of false if unsuccessful
     * @throws \ObjectStorage\ErrorException
     */
    private function tryGetFilePointerForWriting($filename)
    {
        if (is_dir($filename)) {
            throw new \ObjectStorage\ErrorException('Cannot write at ' . $filename);
        }
        $this->setErrorHandler();
        $filePointer = fopen($filename, "c+");
        if ($filePointer !== false) {
            $flockResult = flock($filePointer, LOCK_EX | LOCK_NB);
            if ($flockResult !== false) {
                return $filePointer;
            }
        }
        return false;
    }

    /**
     * Opens object files (main file or metadata file) for writing
     * 
     * @param array $filePointers List of opened files pointers
     * @param string $key The object key
     * @param boolean $openObjectFile The main object file will be opened if TRUE
     * @param boolean $openMetadataFile The metadata object file will be opened if TRUE
     * @return boolean TRUE if successful, FALSE otherwise.
     */
    private function openObjectFilesForWriting(&$filePointers, $key, $openObjectFile, $openMetadataFile)
    {
        $ok = true;
        if (!isset($filePointers[$key])) {
            $filePointers[$key] = [null, null];
        }
        if ($openObjectFile && $filePointers[$key][0] === null) {
            $objectFilePointer = $this->getFilePointerForWriting($this->objectsDir . $key);
            if ($objectFilePointer === false) {
                $ok = false;
            } else {
                $filePointers[$key][0] = $objectFilePointer;
            }
        }

        if ($openMetadataFile && $filePointers[$key][1] === null) {
            $metadataFilePointer = $this->getFilePointerForWriting($this->metadataDir . $key);
            if ($metadataFilePointer === false) {
                $ok = false;
            } else {
                $filePointers[$key][1] = $metadataFilePointer;
            }
        }
        return $ok;
    }

    /**
     * Reads and returns a object file content
     * 
     * @param array $filePointers List of already opened files. If the key requested is not in that list it will be opened.
     * @param string $key The object key
     * @param int The type of object file to read. 0 - The main object file, 1 - The metadata file
     * @return string|null The object file content or null if not existent
     */
    private function getFileContent($filePointers, $key, $fileType)
    {

        if (isset($filePointers[$key], $filePointers[$key][$fileType]) && $filePointers[$key][$fileType] !== null) {
            $pointerPosition = ftell($filePointers[$key][$fileType]);
            fseek($filePointers[$key][$fileType], 0);
            $contents = '';
            while (!feof($filePointers[$key][$fileType])) {
                $contents .= fread($filePointers[$key][$fileType], 8192);
            }
            fseek($filePointers[$key][$fileType], $pointerPosition);
            return $contents;
        } else {
            $filename = ($fileType === 0 ? $this->objectsDir : $this->metadataDir) . $key;
            if (is_file($filename)) {
                if (filesize($filename) === 0) {
                    return '';
                } else {
                    $filePointer = fopen($filename, "r");
                    flock($filePointer, LOCK_SH);
                    $content = fread($filePointer, filesize($filename));
                    fclose($filePointer);
                    return $content;
                }
            }
            return null;
        }
    }

    /**
     * Finds all metadata keys in an result array
     * 
     * @param array $data The result array
     * @return array An array containing all metadata keys
     */
    private function getMetadataFromArray($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 9) === 'metadata.') {
                $result[substr($key, 9)] = $value;
            }
        }
        return $result;
    }

    /**
     * Checks whether there is metadata in the result array specified
     * 
     * @param array $data The result array
     * @return boolean TRUE if metadata exists, FALSE otherwise
     */
    private function hasMetadataInArray($data)
    {
        foreach ($data as $key => $value) {
            if (substr($key, 0, 9) === 'metadata.') {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether the key specified is valid
     * 
     * @param string $key The key to check
     * @return boolean TRUE if the key is valid, FALSE otherwise
     */
    public function isValidKey($key)
    {
        if (!is_string($key) || strlen($key) === 0 || $key === '.' || $key === '..' || strpos($key, '/../') !== false || strpos($key, '/./') !== false || strpos($key, '/') === 0 || strpos($key, './') === 0 || strpos($key, '../') === 0 || substr($key, -2) === '/.' || substr($key, -3) === '/..' || substr($key, -1) === '/') {
            return false;
        }
        return preg_match("/^[a-z0-9\.\/\-\_]*$/", $key) === 1;
    }

    /**
     * Checks whether a metadata key and value are valid
     * 
     * @param string $key The key to check
     * @param string $value The value to check
     * @return boolean TRUE if the key and the value are valid, FALSE otherwise
     */
    private function isValidMetadata($key, $value)
    {
        if (substr($key, 0, 9) !== 'metadata.' || $key === 'metadata.') {
            return false;
        }
        if (!is_string($value)) {
            return false;
        }
        return preg_match("/^[a-zA-Z0-9\.\-\_]*$/", $key) === 1;
    }

    /**
     * Executes list of commmands
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
     * @throws \ObjectStorage\ErrorException
     * @throws \InvalidArgumentException
     * @throws \ObjectStorage\ObjectLockedException
     */
    public function execute($commands)
    {
        $result = [];
        $lockFailure = false;
        $filePointers = [];
        $filesToDelete = [];
        $cache = [];

        $this->setErrorHandler();

        // 1 - validations and caching
        // 2 - lock files
        // 3 - do job
        for ($step = 1; $step <= 3; $step++) {
            if ($step === 1) {
                $this->createDirIfNotExists($this->objectsDir);
                if (!is_writable($this->objectsDir)) {
                    throw new \ObjectStorage\ErrorException('The objectsDir specified (' . $this->objectsDir . ') is not writable');
                }
                if (!is_readable($this->objectsDir)) {
                    throw new \ObjectStorage\ErrorException('The objectsDir specified (' . $this->objectsDir . ') is not readable');
                }
                $this->createDirIfNotExists($this->metadataDir);
                if (!is_writable($this->metadataDir)) {
                    throw new \ObjectStorage\ErrorException('The metadataDir specified (' . $this->metadataDir . ') is not writable');
                }
                if (!is_readable($this->metadataDir)) {
                    throw new \ObjectStorage\ErrorException('The metadataDir specified (' . $this->metadataDir . ') is not readable');
                }
                $this->createDirIfNotExists($this->tempDir);
                if (!is_writable($this->tempDir)) {
                    throw new \ObjectStorage\ErrorException('The tempDir specified (' . $this->tempDir . ') is not writable');
                }
                if (!is_readable($this->tempDir)) {
                    throw new \ObjectStorage\ErrorException('The tempDir specified (' . $this->tempDir . ') is not readable');
                }
            }
            foreach ($commands as $index => $commandData) {
                if ($step === 1) {
                    if (!isset($commandData['command'])) {
                        throw new \InvalidArgumentException('The command attribute is empty at item[' . $index . ']');
                    }
                    if (!is_string($commandData['command'])) {
                        throw new \InvalidArgumentException('The command attribute is empty at item[' . $index . ']');
                    }

                    // where data validation and optimization
                    if (isset($commandData['where'])) {
                        $whereData = [];
                        foreach ($commandData['where'] as $whereDataItem) {
                            $valid = false;
                            if (is_array($whereDataItem) && isset($whereDataItem[0], $whereDataItem[1]) && is_string($whereDataItem[0])) {
                                if (isset($whereData[$whereDataItem[0]])) {
                                    throw new \InvalidArgumentException('where criteria already set');
                                }
                                if ($whereDataItem[0] !== 'key' && $whereDataItem[0] !== 'body' && substr($whereDataItem[0], 0, 9) !== 'metadata.') {
                                    throw new \InvalidArgumentException('invalid where criteria - ' . $whereDataItem[0]);
                                }
                                if (!isset($whereData[$whereDataItem[0]])) {
                                    $whereData[$whereDataItem[0]] = [];
                                }
                                $whereOperator = isset($whereDataItem[2]) ? $whereDataItem[2] : '==';
                                if ($whereOperator !== '==' && $whereOperator !== 'regexp' && $whereOperator !== 'search' && $whereOperator !== 'startsWith') {
                                    throw new \InvalidArgumentException('invalid where operator - ' . $whereOperator);
                                }
                                if (is_string($whereDataItem[1])) {
                                    $valid = true;
                                    $whereData[$whereDataItem[0]][] = [$whereOperator, $whereDataItem[1]];
                                } elseif (is_array($whereDataItem[1])) {
                                    $allValid = sizeof($whereDataItem[1]) > 0;
                                    foreach ($whereDataItem[1] as $valueItem) {
                                        if (!is_string($valueItem)) {
                                            $allValid = false;
                                            break;
                                        }
                                    }
                                    if ($allValid) {
                                        $valid = true;
                                        $temp = array_unique($whereDataItem[1]);
                                        foreach ($temp as $whereDataItemValue) {
                                            $whereData[$whereDataItem[0]][] = [$whereOperator, $whereDataItemValue];
                                        }
                                    }
                                }
                            }
                            if (!$valid) {
                                throw new \InvalidArgumentException('Where data not valid');
                            }
                        }
                        foreach ($whereData as $whereItemKey => $whereItemValueData) {
                            if ($whereItemKey === 'key') {
                                foreach ($whereData['key'] as $whereKeyData) {
                                    if ($whereKeyData[0] === '==') {
                                        if (!$this->isValidKey($whereKeyData[1])) {
                                            throw new \InvalidArgumentException('The key attribute in where data is not valid');
                                        }
                                    }
                                }
                            } elseif (substr($whereItemKey, 0, 9) === 'metadata.') {
                                if (!$this->isValidMetadata($whereItemKey, '')) {
                                    throw new \InvalidArgumentException('The metadata key (' . $whereItemKey . ') in where data is not valid');
                                }
                            }
                        }

                        if (!isset($cache[$index])) {
                            $cache[$index] = [];
                        }
                        $cache[$index]['where'] = $whereData;
                    }
                }
                $command = $commandData['command'];

                // common actions
                if ($command === 'get' || $command === 'add' || $command === 'set' || $command === 'append' || $command === 'delete') {
                    if ($step === 1) {
                        if (!isset($commandData['key'])) {
                            throw new \InvalidArgumentException('key is required for "' . $command . '" command at item[' . $index . ']');
                        }
                        if (!is_string($commandData['key']) || !$this->isValidKey($commandData['key'])) {
                            throw new \InvalidArgumentException('key is not valid');
                        }
                    }
                }
                if ($command === 'get' || $command === 'search') {
                    $resultCodes = isset($commandData['result']) ? $commandData['result'] : [];
                    $metadataNamesResultCodes = [];
                    foreach ($resultCodes as $resultCode) {
                        if (substr($resultCode, 0, 9) === 'metadata.') {
                            $metadataNamesResultCodes[] = substr($resultCode, 9);
                            if (!$this->isValidMetadata($resultCode, '')) {
                                throw new \InvalidArgumentException('The metadata result key (' . $resultCode . ') is not valid');
                            }
                        }
                    }
                    if (!isset($cache[$index])) {
                        $cache[$index] = [];
                    }
                    $cache[$index]['resultCodes'] = $resultCodes;
                    $cache[$index]['metadataNamesResultCodes'] = $metadataNamesResultCodes;
                }

                if ($command === 'add' || $command === 'set' || $command === 'append' || $command === 'delete') {
                    if ($step === 1) {
                        foreach ($commandData as $commandDataKey => $commandDataValue) {
                            if (substr($commandDataKey, 0, 9) === 'metadata.') {
                                if (!$this->isValidMetadata($commandDataKey, $commandDataValue)) {
                                    throw new \InvalidArgumentException('The metadata key (' . $commandDataKey . ') is not valid');
                                }
                            }
                        }
                        if (isset($commandData['body'])) {
                            if (!is_string($commandData['body'])) {
                                throw new \InvalidArgumentException('body is not valid');
                            }
                        }
                    } elseif ($step === 2) {
                        $key = $commandData['key'];
                        if (!$this->openObjectFilesForWriting($filePointers, $key, true, $command === 'delete' || $this->hasMetadataInArray($commandData))) {
                            $lockFailure = true;
                        }
                    } elseif ($step === 3) {
                        $key = $commandData['key'];
                        if ($command === 'delete') {
                            ftruncate($filePointers[$key][0], 0);
                            fseek($filePointers[$key][0], 0);
                            $filesToDelete[$this->objectsDir . $key] = 1;
                            $filesToDelete[$this->metadataDir . $key] = 1;
                        } else {
                            if (isset($filesToDelete[$this->objectsDir . $key])) {
                                unset($filesToDelete[$this->objectsDir . $key]);
                            }
                            if (isset($commandData['body'])) {
                                if ($command === 'add' || $command === 'set') {
                                    ftruncate($filePointers[$key][0], 0);
                                    fseek($filePointers[$key][0], 0);
                                } elseif ($command === 'append') {
                                    fseek($filePointers[$key][0], 0, SEEK_END);
                                }
                                fwrite($filePointers[$key][0], $commandData['body']);
                            }
                        }
                        if ($filePointers[$key][1] !== null) {
                            $metadata = $this->getMetadataFromArray($commandData);
                            $metadataFileSize = filesize($this->metadataDir . $key);
                            $fileMetadata = $metadataFileSize === 0 ? [] : $this->decodeMetadata(fread($filePointers[$key][1], $metadataFileSize));
                            $fileMetadata = array_merge($fileMetadata, $metadata);
                            foreach ($fileMetadata as $fileMetadataKey => $fileMetadataValue) {
                                if ($fileMetadataValue === '') {
                                    unset($fileMetadata[$fileMetadataKey]);
                                }
                            }
                            ftruncate($filePointers[$key][1], 0);
                            fseek($filePointers[$key][1], 0);
                            if (empty($fileMetadata)) {
                                $filesToDelete[$this->metadataDir . $key] = 1;
                            } else {
                                if ($command !== 'delete') {
                                    if (isset($filesToDelete[$this->metadataDir . $key])) {
                                        unset($filesToDelete[$this->metadataDir . $key]);
                                    }
                                    fwrite($filePointers[$key][1], $this->encodeMetaData($fileMetadata));
                                }
                            }
                        }
                        $result[$index] = true;
                    }
                } elseif ($command === 'get') {
                    if ($step === 3) {
                        $objectKey = $commandData['key'];

                        if (isset($filesToDelete[$this->objectsDir . $objectKey])) {
                            $result[$index] = [];
                        } elseif (!is_file($this->objectsDir . $objectKey)) {
                            $result[$index] = [];
                        } else {
                            $resultCodes = $cache[$index]['resultCodes'];
                            $metadataNamesResultCodes = $cache[$index]['metadataNamesResultCodes'];

                            $objectResult = [];
                            if (array_search('key', $resultCodes) !== false) {
                                $objectResult['key'] = $objectKey;
                            }
                            if (array_search('body', $resultCodes) !== false) {
                                $objectResult['body'] = (string) $this->getFileContent($filePointers, $objectKey, 0);
                            }
                            if (array_search('metadata', $resultCodes) !== false || !empty($metadataNamesResultCodes)) {
                                $objectMetadata = $this->getFileContent($filePointers, $objectKey, 1);
                                if ($objectMetadata === null) {
                                    $objectMetadata = [];
                                } else {
                                    $objectMetadata = $this->decodeMetadata($objectMetadata);
                                }
                                if (array_search('metadata', $resultCodes) !== false) {
                                    if (is_array($objectMetadata)) {
                                        foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                            $objectResult['metadata.' . $metadataKey] = $metadataValue;
                                        }
                                    }
                                } elseif (!empty($metadataNamesResultCodes)) {
                                    foreach ($metadataNamesResultCodes as $metadataKey) {
                                        $objectResult['metadata.' . $metadataKey] = is_array($objectMetadata) && isset($objectMetadata[$metadataKey]) ? $objectMetadata[$metadataKey] : '';
                                    }
                                }
                            }
                            $result[$index] = $objectResult;
                        }
                    }
                } elseif ($command === 'search') {

                    if ($step === 3) {
                        // get object keys
                        $hasWhere = isset($cache[$index], $cache[$index]['where']);
                        $getAllKeys = true;
                        $objectsKeys = [];
                        if ($hasWhere && isset($cache[$index]['where']['key'])) {
                            $keysData = $cache[$index]['where']['key'];
                            $keysDone = true;
                            foreach ($keysData as $keyData) {
                                if ($keyData[0] === '==') {
                                    $objectsKeys[] = $keyData[1];
                                } elseif ($keyData[0] === 'startsWith') {
                                    $position = strrpos($keyData[1], '/');
                                    if ($position !== false) {
                                        $dir = substr($keyData[1], 0, $position);
                                        $files = $this->getFiles($this->objectsDir . $dir . '/', true);
                                        foreach ($files as $filename) {
                                            $objectsKeys[] = $dir . '/' . $filename;
                                        }
                                    } else {
                                        $keysDone = false;
                                        break;
                                    }
                                } else {
                                    $keysDone = false;
                                    break;
                                }
                            }
                            if ($keysDone) {
                                $getAllKeys = false;
                            }
                        }
                        if ($getAllKeys) {
                            $objectsKeys = $this->getFiles($this->objectsDir, true);
                        }

                        $resultCodes = $cache[$index]['resultCodes'];
                        $metadataNamesResultCodes = $cache[$index]['metadataNamesResultCodes'];

                        $hasMetadataNamesResultCodes = !empty($metadataNamesResultCodes);

                        $metadataNamesWhereCodes = [];
                        if ($hasWhere) {
                            foreach ($cache[$index]['where'] as $whereCode => $whereValue) {
                                if (substr($whereCode, 0, 9) === 'metadata.') {
                                    $metadataNamesWhereCodes[substr($whereCode, 9)] = $whereValue;
                                }
                            }
                        }
                        $hasMetadataNamesWhereCodes = !empty($metadataNamesWhereCodes);
                        $hasBodyInWhere = isset($cache[$index]['where']['body']);

                        $result[$index] = [];
                        foreach ($objectsKeys as $objectKey) {

                            if (isset($filesToDelete[$this->objectsDir . $objectKey])) {
                                continue;
                            }
                            if (!is_file($this->objectsDir . $objectKey)) {
                                continue;
                            }

                            $objectResult = [];
                            $objectBody = null;
                            $objectMetadata = null;
                            foreach ($resultCodes as $resultCode) {
                                if ($objectBody === null && ($resultCode === 'body' || $hasBodyInWhere)) {
                                    $objectBody = $this->getFileContent($filePointers, $objectKey, 0);
                                }
                                if ($objectMetadata === null && ($resultCode === 'metadata' || $hasMetadataNamesResultCodes || $hasMetadataNamesWhereCodes)) {
                                    $objectMetadata = $this->getFileContent($filePointers, $objectKey, 1);
                                    if ($objectMetadata === null) {
                                        $objectMetadata = [];
                                    } else {
                                        $objectMetadata = $this->decodeMetadata($objectMetadata);
                                    }
                                }
                            }
                            if ($hasWhere) {
                                if (isset($cache[$index]['where']['key'])) {
                                    if (!$this->areWhereConditionsMet($objectKey, $cache[$index]['where']['key'])) {
                                        continue;
                                    }
                                }
                                if (isset($cache[$index]['where']['body'])) {
                                    if (!$this->areWhereConditionsMet($objectBody, $cache[$index]['where']['body'])) {
                                        continue;
                                    }
                                }
                                if ($hasMetadataNamesWhereCodes) {
                                    $found = false;
                                    foreach ($metadataNamesWhereCodes as $whereCode => $whereValue) {
                                        if ($this->areWhereConditionsMet(isset($objectMetadata[$whereCode]) ? $objectMetadata[$whereCode] : '', $whereValue)) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if (!$found) {
                                        continue;
                                    }
                                }
                            }

                            foreach ($resultCodes as $resultCode) {
                                if ($resultCode === 'key') {
                                    $objectResult['key'] = $objectKey;
                                } elseif ($resultCode === 'body') {
                                    $objectResult['body'] = $objectBody;
                                } elseif ($resultCode === 'metadata' || $hasMetadataNamesResultCodes) {
                                    if ($resultCode === 'metadata') {
                                        if (is_array($objectMetadata)) {
                                            foreach ($objectMetadata as $metadataKey => $metadataValue) {
                                                $objectResult['metadata.' . $metadataKey] = $metadataValue;
                                            }
                                        }
                                    } elseif ($hasMetadataNamesResultCodes) {
                                        foreach ($metadataNamesResultCodes as $metadataKey) {
                                            $objectResult['metadata.' . $metadataKey] = is_array($objectMetadata) && isset($objectMetadata[$metadataKey]) ? $objectMetadata[$metadataKey] : '';
                                        }
                                    }
                                }
                            }

                            $result[$index][] = $objectResult;
                        }
                    }
                } elseif ($command === 'duplicate' || $command === 'rename') {
                    if ($step === 1) {
                        if (!isset($commandData['sourceKey'])) {
                            throw new \InvalidArgumentException('sourceKey is required for "' . $command . '" command at item[' . $index . ']');
                        }
                        if (!is_string($commandData['sourceKey']) || !$this->isValidKey($commandData['sourceKey'])) {
                            throw new \InvalidArgumentException('sourceKey is not valid');
                        }
                        if (!isset($commandData['targetKey'])) {
                            throw new \InvalidArgumentException('targetKey is required for "' . $command . '" command at item[' . $index . ']');
                        }
                        if (!is_string($commandData['targetKey']) || !$this->isValidKey($commandData['targetKey'])) {
                            throw new \InvalidArgumentException('targetKey is not valid');
                        }
                        if (!is_file($this->objectsDir . $commandData['sourceKey'])) {
                            throw new \InvalidArgumentException('sourceKey object not found in "' . $command . '" command at item[' . $index . ']');
                        }
                    } elseif ($step === 2) {
                        if ($command === 'duplicate') {
                            if (!$this->openObjectFilesForWriting($filePointers, $commandData['targetKey'], true, true)) {
                                $lockFailure = true;
                            }
                        } elseif ($command === 'rename') {
                            if (!$this->openObjectFilesForWriting($filePointers, $commandData['sourceKey'], true, true)) {
                                $lockFailure = true;
                            }
                            if (!$this->openObjectFilesForWriting($filePointers, $commandData['targetKey'], true, true)) {
                                $lockFailure = true;
                            }
                        }
                    } elseif ($step === 3) {
                        $sourceKey = $commandData['sourceKey'];
                        $targetKey = $commandData['targetKey'];

                        if ($command === 'rename') {
                            $filesToDelete[$this->objectsDir . $sourceKey] = 1;
                            $filesToDelete[$this->metadataDir . $sourceKey] = 1;
                        }
                        if (isset($filesToDelete[$this->objectsDir . $targetKey])) {
                            unset($filesToDelete[$this->objectsDir . $targetKey]);
                        }

                        $objectBody = $this->getFileContent($filePointers, $sourceKey, 0);
                        if ($objectBody === null) {
                            $objectBody = '';
                        }

                        $objectMetadata = $this->getFileContent($filePointers, $sourceKey, 1);

                        if (is_string($objectBody)) {
                            ftruncate($filePointers[$targetKey][0], 0);
                            fseek($filePointers[$targetKey][0], 0);
                            fwrite($filePointers[$targetKey][0], $objectBody);
                        }
                        if (is_string($objectMetadata)) {
                            if (isset($filesToDelete[$this->metadataDir . $targetKey])) {
                                unset($filesToDelete[$this->metadataDir . $targetKey]);
                            }
                            ftruncate($filePointers[$targetKey][1], 0);
                            fseek($filePointers[$targetKey][1], 0);
                            fwrite($filePointers[$targetKey][1], $objectMetadata);
                        }
                        if ($command === 'rename') {
                            ftruncate($filePointers[$sourceKey][0], 0);
                            fseek($filePointers[$sourceKey][0], 0);
                            ftruncate($filePointers[$sourceKey][1], 0);
                            fseek($filePointers[$sourceKey][1], 0);
                        }
                        $result[$index] = true;
                    }
                } else {
                    throw new \InvalidArgumentException('invalid command "' . $command . '" at item[' . $index . ']');
                }
            }

            if ($lockFailure) {
                break;
            }
        }

        foreach ($filePointers as $index => $filePointer) {
            if ($filePointers[$index][0] !== null) {
                fclose($filePointers[$index][0]);
            }
            if ($filePointers[$index][1] !== null) {
                fclose($filePointers[$index][1]);
            }
        }
        unset($filePointers);

        if ($lockFailure) {
            $this->removeErrorHandler();
            throw new \ObjectStorage\ObjectLockedException();
        } else {
            foreach ($filesToDelete as $filename => $one) {
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
            $this->removeErrorHandler();
        }
        return $result;
    }

    /**
     * Checks whether the where conditions are met for a specified value
     * 
     * @param string $value The value to check
     * @param array $conditions List of conditions
     * @return boolean TRUE if the conditions are met, FALSE otherwise.
     */
    private function areWhereConditionsMet($value, $conditions)
    {
        foreach ($conditions as $conditionData) {
            if ($conditionData[0] === '==') {
                if ($value === $conditionData[1]) {
                    return true;
                }
            } elseif ($conditionData[0] === 'regexp') {
                if (preg_match("/" . $conditionData[1] . "/", $value) === 1) {
                    return true;
                }
            } elseif ($conditionData[0] === 'startsWith') {
                if (substr($value, 0, strlen($conditionData[1])) === $conditionData[1]) {
                    return true;
                }
            } elseif ($conditionData[0] === 'search') {
                if (strpos(strtolower($value), strtolower($conditionData[1])) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Creates the directory of the file specified
     * 
     * @param string $filename The filename
     * @return boolean TRUE if successful, FALSE otherwise
     */
    private function createFileDirIfNotExists($filename)
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            if (is_dir($pathinfo['dirname'])) {
                return true;
            } elseif (is_file($pathinfo['dirname'])) {
                return false;
            } else {
                return mkdir($pathinfo['dirname'], 0777, true);
            }
        }
        return false;
    }

    /**
     * Creates a directory if not existent
     * 
     * @param string $dir The directory name
     */
    private function createDirIfNotExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Returns list of files in the directory specified
     * 
     * @param string $dir The directory name
     * @param boolean $recursive If TRUE all files in subdirectories will be returned too
     * @return array An array containing list of all files in the directory specified
     */
    private function getFiles($dir, $recursive = false)
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

    /**
     * Creates an error handler that converts errors into exceptions
     */
    private function setErrorHandler()
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            restore_error_handler();
            throw new \ObjectStorage\ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    /**
     * Removes the error handler registered by setErrorHandler()
     */
    private function removeErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * Converts metadata array into string that will be saved in a file
     * 
     * @param array $metadata The metadata array
     * @return string The result string that will be saved in a file
     * @throws \InvalidArgumentException
     */
    private function encodeMetaData($metadata)
    {
        if (!is_array($metadata)) {
            throw new \InvalidArgumentException('');
        }
        return "content-type:json\n\n" . json_encode($metadata);
    }

    /**
     * Converts metadata from a string format to an array
     * 
     * @param string $metadata The metadata in string format
     * @return array The metadata array
     * @throws \InvalidArgumentException
     */
    private function decodeMetadata($metadata)
    {
        if (!is_string($metadata)) {
            throw new \InvalidArgumentException('');
        }
        if (!isset($metadata{0})) {
            return [];
        }
        $parts = explode("\n\n", $metadata, 2);
        if (!isset($parts[1])) {
            return [];
        }
        return json_decode($parts[1], true);
    }

}
