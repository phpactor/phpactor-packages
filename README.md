Phpactor Packages
=================

This is a mono repository containing most of the Phpactor packages.

Structure
---------

Each package is contained in a namespace:

```
src/<package name>
```

The corresponding test files are in the `tests/` folder:

```
tests/<package name>
```

History
-------

Originally each of the packages existed as an independent GIT repository, but
given that none of them were every stable and nobody used them it only created
a massive maintenance burden.

Most of the repositories have now been consolidated here with the exceptions
of:

- [worse reflection](https://github.com/phpactor/worse-reflection): Static reflection and flow analysis.
- [language-server](https://github.com/phpactor/language-server): Unopinonated language server framework.
- [language-server-protocol](https://github.com/phpactor/language-server-protocol): Transpiled LSP protocol
- [text-document](https://github.com/phpactor/text-document): Value objects for representing source code files.
- [docblock](https://github.com/phpactor/docblock): Sub-standard docblock parsing.
- [class-to-file](https://github.com/phpactor/class-to-file): Determine class names from files and vice-versa.
- [container](https://github.com/phpactor/container): The Phpactor DI container library.
- [map-resolver](https://github.com/phpactor/map-resolver): Similar to the Symfony OptionsResolver (used by the container lib).
- [amp-fs-watch](https://github.com/phpactor/amp-fswatch): Async library to watch for file changes.
- [test-utils](https://github.com/phpactor/test-utils): Various testing utilities (used by above packages).
