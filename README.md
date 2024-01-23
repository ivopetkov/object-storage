# Object Storage

**File-based object storage** with simple API, metadata support, atomic operations and transactions.

[![Latest Stable Version](https://poser.pugx.org/ivopetkov/object-storage/v/stable)](https://packagist.org/packages/ivopetkov/object-storage)
[![License](https://poser.pugx.org/ivopetkov/object-storage/license)](https://packagist.org/packages/ivopetkov/object-storage)

## Install via Composer

```shell
composer require ivopetkov/object-storage
```

## Documentation

Full [documentation](https://github.com/ivopetkov/object-storage/blob/master/docs/markdown/index.md) is available as part of this repository.

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

## License
This project is licensed under the MIT License. See the [license file](https://github.com/ivopetkov/object-storage/blob/master/LICENSE) for more information.

## Contributing
Feel free to open new issues and contribute to the project. Let's make it awesome and let's do in a positive way.

## Author
This library is created and maintained by [Ivo Petkov](https://github.com/ivopetkov/) ([ivopetkov.com](https://ivopetkov.com)).
