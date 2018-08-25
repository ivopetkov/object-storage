# IvoPetkov\ObjectStorage::__construct

Creates a new ObjectStorage instance.

```php
public __construct ( string $dir [, array $options = [] ] )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$dir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the library will store data (the objects, the metadata and the temporary files).

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$options`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List of options. Available values:
- lockRetriesCount - Number of retries to make when waiting for locked (accessed by other scripts) objects.
- lockRetryDelay - Time (in microseconds) between retries when waiting for locked objects.

## Details

Class: [IvoPetkov\ObjectStorage](ivopetkov.objectstorage.class.md)

File: /src/ObjectStorage.php

---

[back to index](index.md)

