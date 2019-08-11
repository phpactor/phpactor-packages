<?php

require __DIR__ .'/../vendor/autoload.php';

use Composer\Semver\Semver;
use Safe\json_decode;
use Safe\file_get_contents;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

$packageMetas = json_decode(file_get_contents(__DIR__ . '/../build/package-meta.json'), true);

$currentDir = getcwd();
$queues = [];
$processes = [];
$exitSum = 0;
$fails = [];

foreach ($packageMetas as $packageName => $packageMeta) {
    echo sprintf('Enqueuing travis scripts for "%s"', $packageMeta['name']).PHP_EOL;
    $vendorPath = $packageMeta['path'] . '/vendor';

    if (file_exists($vendorPath)) {
        $filesystem = new Filesystem();
        $filesystem->remove($vendorPath);
    }

    $travisPath = $packageMeta['path'] . '/.travis.yml';

    if (!file_exists($travisPath)) {
        echo 'Travis doesn\'t exist in this repo, skipping tests'.PHP_EOL;
        continue;
    }

    $travisConfig = Yaml::parseFile($travisPath);

    foreach ($travisConfig['before_script'] ?? [] as $script) {
        $queues = enqueue($queues, $packageName, $script);
    }

    foreach ($travisConfig['script'] ?? [] as $script) {
        $queues = enqueue($queues, $packageName, $script);
    }
}

while ($queues) {
    foreach ($queues as $packageName => $scripts) {
        $packageMeta = $packageMetas[$packageName];
        usleep(5000);

        if (empty($scripts)) {
            unset($queues[$packageName]);
            unset($processes[$packageName]);
            continue;
        }

        if (!isset($processes[$packageName])) {
            [$processes,$scripts] = start_process($processes, $scripts, $packageName, $packageMeta);
            continue;
        }

        $process = $processes[$packageName];

        assert($process instanceof Process);

        if ($process->isRunning()) {
            continue;
        }

        $process->stop();

        // process stopped
        fwrite(STDOUT, sprintf(
            "// \e[%sm%s [%s] %s\e[0m",
            $process->getExitCode() === 0 ? '32' : '31',
            $process->getExitCode(),
            $packageName,
            $process->getCommandLine(),
        ).PHP_EOL);
        $exitSum += $process->getExitCode();

        if ($process->getExitCode() !== 0) {
            unset($queues[$packageName]);
            $fails[] = [ $packageName, $process->getCommandLine(), $process->getOutput(), $process->getErrorOutput()];
            echo $process->getOutput().PHP_EOL;
            echo $process->getErrorOutput().PHP_EOL;
        }

        unset($processes[$packageName]);
        unset($process);

        // start a new one
        [ $processes, $scripts ] = start_process($processes, $scripts, $packageName, $packageMeta);
        $queues[$packageName] = $scripts;
    }
}

foreach ($fails as [ $packageName, $script, $stdOut, $stdErr ]) {
    fwrite(STDOUT, $line = sprintf('Failed: %s / %s', $packageName, $script).PHP_EOL);
    fwrite(STDOUT, str_repeat('=', strlen($line)).PHP_EOL.PHP_EOL);
    fwrite(STDOUT, $stdOut);
    fwrite(STDOUT, $stdErr);
    fwrite(STDOUT, PHP_EOL.PHP_EOL);
}

exit($exitSum);

function start_process(array $processes, array $scripts, string $packageName, array $packageMeta)
{
    if (count($processes) === 10) {
        return [$processes, $scripts];
    }

    $script = array_shift($scripts);
    $workingDir = getcwd() . '/' . $packageMeta['path'];
    fwrite(STDOUT, sprintf('// [%s] [%s] %s', $packageName, $workingDir, $script).PHP_EOL);
    $process = new Process($script, $workingDir);
    $process->start(function ($type, $data) {
    });
    $processes[$packageName] = $process;

    return [$processes, $scripts];
}

function enqueue(array $queues, string $name, string $script): array
{
    if (!isset($queues[$name])) {
        $queues[$name] = [];
    }

    $queues[$name][] = $script;
    return $queues;
}

