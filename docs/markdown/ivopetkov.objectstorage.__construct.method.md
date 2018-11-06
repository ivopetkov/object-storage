# IvoPetkov\ObjectStorage::__construct

Creates a new ObjectStorage instance.

```php
public __construct ( string $objectsDir , string $metadataDir [, array $options = [] ] )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$objectsDir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the library will store the objects.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$metadataDir`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The directory where the library will store the objects metadata.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$options`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List of options. Available values:
- lockRetriesCount - Number of retries to make when waiting for locked (accessed by other scripts) objects.
- lockRetryDelay - Time (in microseconds) between retries when waiting for locked objects.

## Details

Class: [IvoPetkov\ObjectStorage](ivopetkov.objectstorage.class.md)

File: /src/ObjectStorage.php

---

[back to index](index.md)

