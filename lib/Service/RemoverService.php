<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\DependentExtensionFinder;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Extensions;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Model\RemoveExtension;
use RuntimeException;

class RemoverService
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var DependentExtensionFinder
     */
    private $finder;

    /**
     * @var ExtensionConfig
     */
    private $config;

    /**
     * @var ExtensionRepository
     */
    private $repository;

    public function __construct(
        Installer $installer,
        DependentExtensionFinder $finder,
        ExtensionRepository $repository,
        ExtensionConfig $config
    ) {
        $this->installer = $installer;
        $this->finder = $finder;
        $this->config = $config;
        $this->repository = $repository;
    }

    public function findDependentExtensions(array $extensionNames): Extensions
    {
        return $this->finder->findDependentExtensions($extensionNames);
    }

    public function install()
    {
        $this->installer->install();
    }

    public function installForceUpdate()
    {
        $this->installer->installForceUpdate();
    }

    public function removeExtension(string $extensionName): void
    {
        $extension = $this->repository->find($extensionName);

        if ($extension->isPrimary()) {
            throw new RuntimeException(
                'Extension is a primary extension and cannot be removed'
            );
        }

        $this->config->unrequire($extensionName);
        $this->config->commit();
    }
}
