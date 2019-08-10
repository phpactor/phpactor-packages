<?php

require __DIR__ .'/../vendor/autoload.php';

use Composer\Semver\Semver;
use Safe\json_decode;
use Safe\file_get_contents;

$composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

$replace = [];
$autoload = [];
$require = [];
$requires = [];

foreach ($packageMetas as $packageMeta) {
    foreach ($packageMeta['require'] as $name => $version) {
        if (!isset($requires[$name])) {
            $requires[$name] = [];
        }

        $requires[$name][] = $version;
    }
}

foreach ($requires as $packageName => $versions) {
    if (0 === strpos($packageName, 'phpactor/')) {
        continue;
    }
    sort($versions);
    $require[$packageName] = array_pop($versions);
}

foreach ($packageMetas as $shortName => $packageMeta) {
    $replace[$packageMeta['name']] = $packageMeta['version'];
    $autoload = array_merge_recursive($autoload, $packageMeta['autoload']);
}

$composer['require'] = $require;
$composer['autoload'] = $autoload;
$composer['replace'] = $replace;

echo json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
