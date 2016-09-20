# Object Storage

**File-based object storage** with simple API, metadata support, atomic operations and transactions.

[![Build Status](https://travis-ci.org/ivopetkov/object-storage.svg)](https://travis-ci.org/ivopetkov/object-storage)
[![Latest Stable Version](https://poser.pugx.org/ivopetkov/object-storage/v/stable)](https://packagist.org/packages/ivopetkov/object-storage)
[![codecov.io](https://codecov.io/github/ivopetkov/object-storage/coverage.svg?branch=master)](https://codecov.io/github/ivopetkov/object-storage?branch=master)
[![License](https://poser.pugx.org/ivopetkov/object-storage/license)](https://packagist.org/packages/ivopetkov/object-storage)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c9ad5d49897f4c209236225b7d0c1c1c)](https://www.codacy.com/app/ivo_2/object-storage)

## Install via Composer

```shell
composer require ivopetkov/object-storage
```

## Example

```php
$storage = new \IvoPetkov\ObjectStorage('path/to/the/data/dir/');

// Save data
$storage->set([
    'key' => 'books/1449392776',
    'body' => 'book 1449392776 content in pdf format',
    'metadata.title' => 'Programming PHP',
    'metadata.authors' => '["Kevin Tatroe", "Peter MacIntyre", "Rasmus Lerdorf"]',
    'metadata.year' => '2013'
]);

// Retrieve data
$result = $storage->get([
    'key' => 'books/1449392776',
    'result' => ['body', 'metadata.title']
]);
// Array
// (
//     [body] => 'book 1449392776 content in pdf format'
//     [metadata.title] => 'Programming PHP'
// )
```

## Documentation

There is only one class that you must use to create object storage instance.

### Classes

#### IvoPetkov\ObjectStorage
##### Constants

`const string VERSION`

##### Properties

`public string $objectsDir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the objects will be stored

`public string $metadataDir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the objects metadata will be stored

`public string $tempDir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where temp library data will be stored

`public int $lockRetriesCount`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of retries to make when waiting for locked (accessed by other scripts) objects

`public int $lockRetryDelay`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time (in microseconds) between retries when waiting for locked objects

##### Methods

```php
public __construct ( [ string $dir = 'data/' ] )
```

Creates a new Object storage instance

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$dir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the library will store data (the objects, the metadata and the temporary files)

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public array get ( array $parameters )
```

Retrieves object data for a specified key

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['key' => 'example1', 'result' => ['body', 'metadata.year']]

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing the result data if existent, empty array otherwise

```php
public boolean set ( array $parameters )
```

Saves object data for a specified key

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['key' => 'example1', 'body' => 'body1', 'metadata.year' => '2000']

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if successful, FALSE otherwise

```php
public boolean append ( array $parameters )
```

Appends object data for a specified key. The object will be created if not existent.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['key' => 'example1', 'body' => 'body1']

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public boolean duplicate ( array $parameters )
```

Creates a copy of an object. It's metadata is copied too.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public boolean rename ( array $parameters )
```

Renames an object

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['sourceKey' => 'example1', 'targetKey' => 'example2']

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public boolean delete ( array $parameters )
```

Deletes an object and it's metadata

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['key' => 'example1']

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public array search ( array $parameters )
```

Retrieves a list of all object matching the criteria specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format:

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public boolean isValidKey ( string $key )
```

Checks whether the key specified is valid

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$key`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The key to check

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE if the key is valid, FALSE otherwise

```php
public array execute ( array $commands )
```

Executes list of commmands

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$commands`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Array containing list of commands in the following format:

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Array containing the results for the commands

## License
Object Storage is open-sourced software. It's free to use under the MIT license. See the [license file](https://github.com/ivopetkov/object-storage/blob/master/LICENSE) for more information.

## Author
This library is created by Ivo Petkov. Feel free to contact me at [@IvoPetkovCom](https://twitter.com/IvoPetkovCom) or [ivopetkov.com](https://ivopetkov.com).
