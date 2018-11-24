<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Exception;
use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Model\Exception\CouldNotInstallExtension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfigLoader;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Model\VersionFinder;

class InstallerService
{
    /**
     * @var Installer
     */
    private $installer;
    private $config;

    /**
     * @var VersionFinder
     */
    private $finder;

    /**
     * @var ExtensionRepository
     */
    private $extensionRepository;

    /**
     * @var ExtensionConfigFactory
     */
    private $configFactory;

    /**
     * @var ProgressLogger
     */
    private $progress;

    public function __construct(
        Installer $installer,
        ExtensionConfigLoader $configFactory,
        VersionFinder $finder,
        ExtensionRepository $extensionRepository,
        ProgressLogger $progress
    ) {
        $this->installer = $installer;
        $this->finder = $finder;
        $this->extensionRepository = $extensionRepository;
        $this->configFactory = $configFactory;
        $this->progress = $progress;
    }

    public function requireExtensions(array $extensions): string
    {
        $config = $this->configFactory->load();

        foreach ($extensions as $extension) {
            $version = $this->finder->findBestVersion($extension);
            $this->progress->log(sprintf('Using version "%s"', $version));
            $config->require($extension, $version);
        }

        $config->write();
        $this->installForceUpdate($config);

        return $version;
    }

    public function install(): void
    {
        $this->installer->install();
    }

    public function installForceUpdate(ExtensionConfig $config = null)
    {
        if (!$config) {
            $this->installer->installForceUpdate();
        }

        try {
            $this->installer->installForceUpdate();
        } catch (Exception $couldNotInstall) {
            $config->revert();
            throw new CouldNotInstallExtension($couldNotInstall->getMessage(), null, $couldNotInstall);
        }
    }
}
