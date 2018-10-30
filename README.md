File Path Resolver
==================

[![Build Status](https://travis-ci.org/phpactor/app-path-resolver.svg?branch=master)](https://travis-ci.org/phpactor/app-path-resolver)

Resolves file paths by filtering and replacing tokens with values.

- Canonicalization support via `webmozart/path-util`
- XDG directory expansion via `dnoegel/php-xdg-base-dir`

Usage
-----

```php
$pathResolver = new PathResolver([
    new CanonicalizationFilter(),
    new TokenExpandingFilter([
        new ValueExpander('%my_token%', 'my_value'),
        new XdgCacheExpander('%xdg_cache%'),
        new XdgConfigExpander('%xdg_conifg%'),
        new CallbackExpander('%callback%, function () {
            return 'hello from callback';
        });
    ])
]);

$pathResolver->resolve('/foo/../foo/%my_token%'); // foo/my_value
$pathResolver->resolve('%xdg_home%/my_app'); // /home/user/.config/my_app
$pathResolver->resolve('%callback%'); // hello from callback
```
