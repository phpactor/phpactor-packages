<?php

require __DIR__ .'/../vendor/autoload.php';

use Composer\Semver\Semver;
use Safe\json_decode;
use Safe\file_get_contents;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

$currentDir = getcwd();
$queues = [];
$processes = [];
$exitSum = 0;
$fails = [];

foreach ($packageMetas as $packageMeta) {
    echo sprintf('Trying to run travis scripts for "%s"', $packageMeta['name']).PHP_EOL;

    $travisPath = $packageMeta['path'] . '/.travis.yml';

    if (!file_exists($travisPath)) {
        echo 'Travis doesn\'t exist in this repo, skipping tests'.PHP_EOL;
        continue;
    }

    $travisConfig = Yaml::parseFile($travisPath);

    foreach ($travisConfig['before_script'] ?? [] as $script) {
        $queues = enqueue($queues, $packageMeta['name'], $script);
    }

    foreach ($travisConfig['script'] ?? [] as $script) {
        $queues = enqueue($queues, $packageMeta['name'], $script);
    }
}

while ($queues) {
    foreach ($queues as $packageName => &$scripts) {
        usleep(5000);

        if (empty($scripts)) {
            unset($queues[$packageName]);
            unset($processes[$packageName]);
            continue;
        }

        if (!isset($processes[$packageName])) {
            $processes = start_process($processes, $scripts, $packageName, $packageMeta);
            continue;
        }

        $process = $processes[$packageName];

        assert($process instanceof Process);

        if ($process->isRunning()) {
            continue;
        }

        $process->stop();

        // process stopped
        fwrite(STDOUT, sprintf('// [%s] %s EXITED %s', $packageName, $script, $process->getExitCode()).PHP_EOL);
        $exitSum += $process->getExitCode();

        if ($process->getExitCode() !== 0) {
            $fails[] = [ $packageName, $process->getCommandLine()];
        }

        unset($processes[$packageName]);
        unset($process);

        // start a new one
        $processes = start_process($processes, $scripts, $packageName, $packageMeta);
    }
}

foreach ($fails as [ $packageName, $script ]) {
    fwrite(STDOUT, sprintf('Failed: %s / %s', $packageName, $script).PHP_EOL);
}

exit($exitSum);

function start_process(array $processes, array &$scripts, string $packageName, array $packageMeta)
{
    if (count($processes) === 5) {
        return $processes;
    }

    $script = array_shift($scripts);
    fwrite(STDOUT, sprintf('// [%s] %s', $packageName, $script).PHP_EOL);
    $process = new Process($script, $packageMeta['path']);
    $process->start(function ($type, $data) {
    });
    $processes[$packageName] = $process;

    return $processes;
}

function enqueue(array $queues, string $name, string $script): array
{
    if (!isset($queues[$name])) {
        $queues[$name] = [];
    }

    $queues[$name][] = $script;
    return $queues;
}

