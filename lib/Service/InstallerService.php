<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Model\VersionFinder;

class InstallerService
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ExtensionConfig
     */
    private $config;

    /**
     * @var VersionFinder
     */
    private $finder;

    public function __construct(Installer $installer, ExtensionConfig $config, VersionFinder $finder)
    {
        $this->installer = $installer;
        $this->config = $config;
        $this->finder = $finder;
    }

    public function addExtension($extension): string
    {
        $version = $this->finder->findBestVersion($extension);
        $this->config->require($extension, $version);
        $this->config->commit();

        return $version;
    }

    public function install(): void
    {
        $this->installer->install();
    }

    public function installForceUpdate(): void
    {
        $this->installer->installForceUpdate();
    }
}
