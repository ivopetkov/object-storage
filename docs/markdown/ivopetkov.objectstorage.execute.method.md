# IvoPetkov\ObjectStorage::execute

Executes list of commands.

```php
public array execute ( array $commands )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$commands`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Array containing list of commands in the following format:
[
'command' => 'set',
'key' => 'example1',
'body' => 'body1'
],
[
'command' => 'append',
'key' => 'example2',
'body' => 'body2'
]

## Returns

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Array containing the results for the commands.

## Details

Class: [IvoPetkov\ObjectStorage](ivopetkov.objectstorage.class.md)

File: /src/ObjectStorage.php

---

[back to index](index.md)

