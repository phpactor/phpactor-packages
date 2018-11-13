<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\Version\VersionSelector;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\RemoveExtension;
use RuntimeException;

class ComposerRemoveExtension implements RemoveExtension
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $configFilePath;

    /**
     * @var VersionSelector
     */
    private $versionSelector;

    /**
     * @var string
     */
    private $originalFile;

    public function __construct(RepositoryInterface $repository, string $configFilePath)
    {
        $this->repository = $repository;
        $this->configFilePath = $configFilePath;
    }

    public function remove(string $extension): bool
    {
        return $this->removeExtensionFromConfig($extension);
    }

    private function removeExtensionFromConfig(string $extension): bool
    {
        if (!file_exists($this->configFilePath)) {
            throw new RuntimeException(sprintf(
                'File %s does not exist', $this->configFilePath
            ));
        }

        $this->originalFile = (string) file_get_contents($this->configFilePath);
        
        $config = json_decode($this->originalFile, true);
        
        if (!isset($config['require'])) {
            return false;
        }
        
        unset($config['require'][$extension]);
        if (empty($config['require'])) {
            unset($config['require']);
        }
        
        file_put_contents($this->configFilePath, json_encode($config, JSON_PRETTY_PRINT));

        return true;
    }
}
