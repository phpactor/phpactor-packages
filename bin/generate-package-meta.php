<?php

require __DIR__ . '/../vendor/autoload.php';
use Safe\json_decode;
use Safe\file_get_contents;

$meta = [];
foreach (['library', 'extensions'] as $dirName) {
    foreach (array_filter(scandir($dirName), function (string $path) {
        return !in_array($path, ['.', '..']);
    }) as $packageName) {
        $path = $dirName . '/' . $packageName;
        $packageMeta = [];

        $composerData = composer_data($path);
        
        $packageMeta['name'] = 'phpactor/'.$packageName;
        $packageMeta['autoload'] = autoload_data($path, $composerData['autoload']);
        $packageMeta['version'] = git_version($packageName);
        $packageMeta['require'] = $composerData['require'] ?? [];
        $meta[$packageName] = $packageMeta;
    }
}

echo json_encode($meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

function composer_data(string $path): array
{
    return json_decode(file_get_contents($path . '/composer.json'), true);
}
function autoload_data(string $path, array $composerAutoload)
{
    $data = [];
    foreach ($composerAutoload['psr-4'] as $namesace => $autoloadPath) {
        $data['psr-4'][$namesace] = $path . '/' . $autoloadPath;
    }

    return $data;
}
function git_version(string $packageName)
{
    $versions = array_map(function (string $packageVersion) use ($packageName) {
        return substr($packageVersion, strlen($packageName) + 1);
    }, array_filter(explode("\n",`git tag`), function (string $tagName) use ($packageName) {
        return 0 === strpos($tagName, $packageName . '/');
    }));
    sort($versions);

    if (count($versions) === 0) {
        return 'dev-master';
    }

    return array_pop($versions);
}
