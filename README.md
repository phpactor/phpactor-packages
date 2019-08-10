Phpactor Packages
=================

This repository contains all Phpactor libraries and extensions in addition to
a `bin/phpactor` binary which integrates all of the extensions in this
mono-repository.

Although this package can be used as a standalone Phpactor, it is currently
recommended to use the (more featured) down-stream
[phpactor/phpactor](https://github.com/phpactor/phpactor) distribution.

Building
--------

Run `php scripts/build.php`. This will in turn execute:

- `scripts/generate-package-meta.php`: Generates a JSON file containing
  metadata on all of the sub-packages in this repo.
- `scripts/generates-composer-json.php`: Generate the `composer.json` file for
  this repository based on the package metadata.
