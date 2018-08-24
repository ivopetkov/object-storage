# IvoPetkov\ObjectStorage::set

Saves object data for a specified key.

```php
public void set ( array $parameters )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$parameters`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data in the following format: ['key' => 'example1', 'body' => 'body1', 'metadata.year' => '2000']. Specifying metadata.* will bulk remove/update all previous metadata.

## Returns

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

## Details

Class: [IvoPetkov\ObjectStorage](ivopetkov.objectstorage.class.md)

File: /src/ObjectStorage.php

---

[back to index](index.md)

