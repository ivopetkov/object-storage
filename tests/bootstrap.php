<?php

require __DIR__ . '/../vendor/autoload.php';

function emptyDir($dir)
{
    $dataFiles = getFilesInDir($dir);
    foreach ($dataFiles as $file) {
        if (is_file($dir . $file)) {
            unlink($dir . $file);
        } elseif (is_dir($dir . $file)) {
            rmdir($dir . $file);
        }
    }
}

function removeDir($dir)
{
    emptyDir($dir);
    if (is_dir($dir)) {
        rmdir($dir);
    }
}

function checkState($expectedState)
{
    $md5 = substr($expectedState, 0, 32);
    return $md5 !== md5(getState());
}

function getState()
{
    $files = getFilesInDir('data/');
    sort($files);
    $result = '';
    foreach ($files as $filename) {
        if (is_file('data/' . $filename)) {
            $result .= $filename . ': ' . file_get_contents('data/' . $filename) . "\n";
        }
    }
    return md5($result) . "\n" . $result;
}

function getFilesInDir($dir)
{
    $result = [];
    if (is_dir($dir)) {
        $list = scandir($dir);
        if (is_array($list)) {
            foreach ($list as $filename) {
                if ($filename != '.' && $filename != '..') {

                    if (is_dir($dir . $filename)) {
                        $dirResult = getFilesInDir($dir . $filename . '/', true);
                        if (!empty($dirResult)) {
                            foreach ($dirResult as $index => $value) {
                                $dirResult[$index] = $filename . '/' . $value;
                            }
                            $result = array_merge($result, $dirResult);
                        }
                    }
                    $result[] = $filename;
                }
            }
        }
    }
    return $result;
}
