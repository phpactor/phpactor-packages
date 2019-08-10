<?php

require __DIR__ .'/../vendor/autoload.php';

use Composer\Semver\Semver;
use Safe\json_decode;
use Safe\file_get_contents;

$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

fwrite(STDOUT, '<?php return [');
foreach ($packageMetas as $name => $packageMeta) {
    fwrite(STDOUT, sprintf(
        "'%s' => 'git@github.com:phpactor/%s.git',",
        $packageMeta['type'] . '/' . $packageMeta['name'],
        $name
    ).PHP_EOL);
}

fwrite(STDOUT, '];');
