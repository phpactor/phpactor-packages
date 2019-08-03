<?php

use Safe\json_decode;
use Safe\file_get_contents;

$composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

$replace = [];
$autoload = [];
foreach ($packageMetas as $shortName => $packageMeta) {
    $replace[$packageMeta['name']] = $packageMeta['version'];
    $autoload = array_merge_recursive($autoload, $packageMeta['autoload']);
}

$composer['autoload'] = $autoload;
$composer['replace'] = $replace;

echo json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
