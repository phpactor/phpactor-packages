Reference Finder
================

[![Build Status](https://travis-ci.org/phpactor/definition-jumper.svg?branch=master)](https://travis-ci.org/phpactor/definition-jumper)

Base package for finding references to a symbol at a given offset in a given
text file.

For example, provide:

- the location (URI and byte offset) for the definition of the symbol
  in a text file under the current cursor position.
- locations for references to the symbol at the given offset.
