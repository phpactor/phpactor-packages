<?php

use Safe\json_decode;
use Safe\file_get_contents;

$composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

$replace = [];
$autoload = [];
$require = [];

foreach ($packageMetas as $shortName => $packageMeta) {
    $replace[$packageMeta['name']] = $packageMeta['version'];
    $autoload = array_merge_recursive($autoload, $packageMeta['autoload']);
    $require = array_filter(array_merge($require, $packageMeta['require']), function (string $package) use ($packageMeta, $packageMetas) {
        return !in_array($package, array_map(function (string $name) {
            return 'phpactor/' . $name;
        }, array_keys($packageMetas)));
    }, ARRAY_FILTER_USE_KEY);
}

$composer['require'] = $require;
$composer['autoload'] = $autoload;
$composer['replace'] = $replace;

echo json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
