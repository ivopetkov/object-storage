# IvoPetkov\ObjectStorage\ErrorException

extends [ErrorException](http://php.net/manual/en/class.errorexception.php)

implements [Throwable](http://php.net/manual/en/class.throwable.php)

Exception that will be thrown when the library error occurs.

## Properties

### Inherited from [ErrorException](http://php.net/manual/en/class.errorexception.php):

##### protected int $severity

### Inherited from [Exception](http://php.net/manual/en/class.exception.php):

##### protected int $code

##### protected  $file

##### protected  $line

##### protected string $message

## Methods

### Inherited from [ErrorException](http://php.net/manual/en/class.errorexception.php):

##### public [__construct](http://php.net/manual/en/errorexception.__construct.php) ( [  $message [,  $code [,  $severity [,  $filename [,  $lineno [,  $previous ]]]]]] )

##### public final void [getSeverity](http://php.net/manual/en/errorexception.getseverity.php) ( void )

### Inherited from [Exception](http://php.net/manual/en/class.exception.php):

##### public final void [getCode](http://php.net/manual/en/exception.getcode.php) ( void )

##### public final void [getFile](http://php.net/manual/en/exception.getfile.php) ( void )

##### public final void [getLine](http://php.net/manual/en/exception.getline.php) ( void )

##### public final void [getMessage](http://php.net/manual/en/exception.getmessage.php) ( void )

##### public final void [getPrevious](http://php.net/manual/en/exception.getprevious.php) ( void )

##### public final void [getTrace](http://php.net/manual/en/exception.gettrace.php) ( void )

##### public final void [getTraceAsString](http://php.net/manual/en/exception.gettraceasstring.php) ( void )

## Details

File: /src/ObjectStorage/ErrorException.php

---

[back to index](index.md)

