# IvoPetkov\ObjectStorage::search

Retrieves a list of all object matching the criteria specified.

```php
public array search ( array $parameters )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format:
// Finds objects by key
[
'where' => [
['key', ['book-1449392776', 'book-1430268158']]
],
'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
]
// Finds objects by metadata
[
'where' => [
['metadata.year', '2013']
],
'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
]
// Finds objects by regular expression
[
'where' => [
['key', '^prefix1\/', 'regExp']
],
'result' => ['key', 'body', 'body.length', 'body.range(*,*)', 'metadata.title']
]

## Returns

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing all matching objects.

## Details

Class: [IvoPetkov\ObjectStorage](ivopetkov.objectstorage.class.md)

File: /src/ObjectStorage.php

---

[back to index](index.md)

