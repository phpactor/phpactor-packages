<?php

namespace Phpactor\TestUtils;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use InvalidArgumentException;
use RuntimeException;
use SplFileInfo;

class Workspace
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function create(string $path): self
    {
        if (empty($path)) {
            throw new RuntimeException(
                'Workspace path cannot be empty'
            );
        }

        return new self($path);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->path($path));
    }

    public function path(string $path): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $path;
    }

    public function getContents(string $path): string
    {
        if (false === $this->exists($path)) {
            throw new InvalidArgumentException(sprintf(
                'File "%s" does not exist',
                $path
            ));
        }

        $contents = file_get_contents($this->path($path));

        if (false === $contents) {
            throw new RuntimeException('file_get_contents returned false');
        }

        return $contents;
    }

    public function reset()
    {
        if (file_exists($this->path)) {
            $this->remove($this->path);
        }

        mkdir($this->path);
    }

    public function put(string $path, string $contents): Workspace
    {
        if (!$this->exists(dirname($path))) {
            $this->mkdir(dirname($path));
        }

        file_put_contents($this->path($path), $contents);

        return $this;
    }

    public function mkdir($path): Workspace
    {
        $path = $this->path($path);

        if (file_exists($path)) {
            throw new InvalidArgumentException(sprintf(
                'Node "%s" already exists, cannot create directory',
                $path
            ));
        }

        mkdir($path, 0777, true);

        return $this;
    }

    public function loadManifest(string $manifest)
    {
        foreach ($this->parseManifest($manifest) as $path => $contents) {
            $path = $this->path . DIRECTORY_SEPARATOR . $path;

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, $contents);
        }
    }

    private function parseManifest(string $manifest)
    {
        $lines = explode(PHP_EOL, $manifest);

        $buffer = [];
        $currentFile = null;

        foreach ($lines as $line) {
            $match = preg_match('{//\s?File:\s?(.*)}', $line, $matches);

            if ($match) {
                $currentFile = $matches[1];
                continue;
            }

            if (null === $currentFile) {
                continue;
            }

            if (!isset($buffer[$currentFile])) {
                $buffer[$currentFile] = [];
            }

            $buffer[$currentFile][] = $line;
        }

        return array_map(function (array $lines) {
            return implode(PHP_EOL, $lines);
        }, $buffer);
    }

    private function remove($path = '')
    {
        if ($path) {
            $splFileInfo = new SplFileInfo($path);

            if (in_array($splFileInfo->getType(), ['socket', 'file', 'link'])) {
                unlink($path);
                return;
            }
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $this->remove($file->getPathName());
        }

        rmdir($path);
    }
}
