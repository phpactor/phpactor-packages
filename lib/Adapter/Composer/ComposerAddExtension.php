<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionSelector;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use RuntimeException;

class ComposerAddExtension implements AddExtension
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

    public function __construct(RepositoryInterface $repository, string $configFilePath, VersionSelector $versionSelector)
    {
        $this->repository = $repository;
        $this->configFilePath = $configFilePath;
        $this->versionSelector = $versionSelector;
    }

    public function add($extension): string
    {
        $version = $this->versionSelector->findBestCandidate($extension);

        if (!$version) {
            throw new RuntimeException(sprintf('Could not find extension "%s"', $extension));
        }

        return $this->updateFile($extension, $version);
    }

    private function updateFile($extension, PackageInterface $version)
    {
        $this->originalFile = file_get_contents($this->configFilePath);
        
        $config = json_decode($this->originalFile, true);
        if (!isset($config['require'])) {
            $config['require'] = [];
        }
        
        $config['require'][$extension] = $version->getPrettyVersion();
        
        file_put_contents($this->configFilePath, json_encode($config, JSON_PRETTY_PRINT));
        
        return $version->getPrettyVersion();
    }
}
