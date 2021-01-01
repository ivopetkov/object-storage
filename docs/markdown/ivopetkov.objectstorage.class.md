# IvoPetkov\ObjectStorage

Enables storing and manipulating data objects in the directory specified.

## Methods

##### public [__construct](ivopetkov.objectstorage.__construct.method.md) ( string $objectsDir , string $metadataDir [, array $options = [] ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a new ObjectStorage instance.

##### public void [append](ivopetkov.objectstorage.append.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Appends object data for a specified key. The object will be created if not existent.

##### public void [delete](ivopetkov.objectstorage.delete.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deletes an object and it's metadata.

##### public void [duplicate](ivopetkov.objectstorage.duplicate.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a copy of an object. It's metadata is copied too.

##### public array [execute](ivopetkov.objectstorage.execute.method.md) ( array $commands )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Executes list of commands.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Array containing the results for the commands.

##### public bool [exists](ivopetkov.objectstorage.exists.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Checks if the specified object exists.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: Returns TRUE if the object exists, FALSE otherwise.

##### public array|null [get](ivopetkov.objectstorage.get.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieves object data for the specified key.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: An array containing the result data if existent, NULL otherwise.

##### public void [rename](ivopetkov.objectstorage.rename.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Renames an object.

##### public array [search](ivopetkov.objectstorage.search.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retrieves a list of all object matching the criteria specified.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: An array containing all matching objects.

##### public void [set](ivopetkov.objectstorage.set.method.md) ( array $parameters )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Saves object data for a specified key.

##### public bool [validate](ivopetkov.objectstorage.validate.method.md) ( string $key )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Checks whether the key specified is valid.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns: TRUE if the key is valid, FALSE otherwise.

## Details

File: /src/ObjectStorage.php

---

[back to index](index.md)

