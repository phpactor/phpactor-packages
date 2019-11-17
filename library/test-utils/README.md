Test Utils
==========

Set of general test utilties for Phpactor.

Workspace
---------

The `Workspace` class is used to manage a test file workspace.

### Resetting / Creating a workspace

```php
$workspace = Workspace::create(__DIR__ . '/workspace');
$workspace->reset(); // creates or deletes then creates the workspace directory
```

### Loading test files

Load a set of test files from a "manifest":

```php
$manifest = <<<'EOT'
// File: lib/ClassOne.php
<?php

class ClassOne {}
// File: lib/Foo/ClassTwo.php
<?php namespace Foo;

class ClassTwo {}
EOT
;

$workspace = Workspace::create(__DIR__ . '/workspace');
$workspace->loadManifest($manifest); // create the files in the manifest

Assert::assertTrue($workspace->exists('lib/ClassOne.php'));
Assert::assertTrue($workspace->exists('lib/Foo/ClassTwo.php'));

echo $workspace->getContents('/lib/Foo/ClassTwo.php');
```
