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
    }

    private function commit($config)
    {
        file_put_contents($this->configFilePath, json_encode($config, JSON_PRETTY_PRINT));
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

        if (false === $config) {
            throw new RuntimeException(sprintf(
                'Invalid JSON file "%s"', $this->path
            ));
        }

        return $config;
    }
}
