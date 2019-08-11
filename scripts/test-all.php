<?php

require __DIR__ .'/../vendor/autoload.php';

use Composer\Semver\Semver;
use Safe\json_decode;
use Safe\file_get_contents;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);
$rootDir = getcwd();
$rootVendorDir = $rootDir . '/vendor';

$filesystem = new Filesystem();
foreach ($packageMetas as $packageMeta) {
    echo $title = 'Testing: ' . $packageMeta['name'] . PHP_EOL;
    echo str_repeat('=', strlen($title));
    echo PHP_EOL.PHP_EOL;
    $vendorPath = $packageMeta['path'] . '/vendor';

    if (file_exists($vendorPath)) {
        $filesystem->remove($vendorPath);
    }

    // create symlink to monorepo vendor dir
    symlink($rootVendorDir, $vendorPath);
    $process = new Process('./vendor/bin/phpunit', $packageMeta['path']);
    $process->run(function ($type, $out) {
        echo $out;
    });
    echo PHP_EOL;
}
