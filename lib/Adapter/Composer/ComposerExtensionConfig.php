<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use RuntimeException;

class ComposerExtensionConfig implements ExtensionConfig
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $config;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->config = $this->read();
    }

    public function require(string $extension, string $version): void
    {
        if (!isset($this->config['require'])) {
            $this->config['require'] = [];
        }
        
        $this->config['require'][$extension] = $version;
    }

    public function unrequire(string $extension): void
    {
        if (!isset($this->config['require'][$extension])) {
            return;
        }

        unset($this->config['require'][$extension]);

        if (empty($this->config['require'])) {
            unset($this->config['require']);
        }
    }

    public function commit(): void
    {
        file_put_contents($this->path, json_encode($this->config, JSON_PRETTY_PRINT));
    }

    private function read(): array
    {
        if (!file_exists($this->path)) {
            throw new RuntimeException(sprintf(
                'Extension config "%s" does not exist', $this->path
            ));
        }

        $contents = (string) file_get_contents($this->path);
        $config = json_decode($contents, true);

        if (null === $config) {
            throw new RuntimeException(sprintf(
                'Invalid JSON file "%s"', $this->path
            ));
        }

        return $config;
    }
}
