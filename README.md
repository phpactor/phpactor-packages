Phpactor Packages
=================

[![Build Status](https://travis-ci.org/phpactor/phpactor-packages.svg?branch=master)](https://travis-ci.org/phpactor/phpactor-packages)

*WORK IN PROGRESS*

This repository contains all Phpactor libraries and extensions in addition to
a `bin/phpactor` binary which integrates all of the extensions in this
mono-repository.

Although this package can be used as a standalone Phpactor, it is currently
recommended to use the (more featured) down-stream
[phpactor/phpactor](https://github.com/phpactor/phpactor) distribution.

Adminstration
-------------

### Generate version report

Report the current version status for all packages:

```
./vendor/bin/maestro run -rversion --filter='task["alias"]=="survey"'
```

### Pull upstream packages

NOTE: This is only necessary before we switch to a monorepo.

```
# extensions
./vendor/bin/maestro run --report=json --no-loop \
    | jq '.nodes[] | select(.prototype == "extension") | .name' -r \
    | xargs -I % git subtree pull --prefix=extension/% git@github.com:phpactor/% master

# libraries
./vendor/bin/maestro run --report=json --no-loop \
    | jq '.nodes[] | select(.prototype == "library") | .name' -r \
    | xargs -I % git subtree pull --prefix=libraru/% git@github.com:phpactor/% master
```
