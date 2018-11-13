<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\DependentExtensionFinder;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Model\RemoveExtension;

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
     * @var RemoveExtension
     */
    private $removeExtension;

    public function __construct(
        Installer $installer,
        DependentExtensionFinder $finder,
        RemoveExtension $removeExtension
    )
    {
        $this->removeExtension = $removeExtension;
        $this->installer = $installer;
        $this->finder = $finder;
    }

    public function findDependentExtensions(array $extensionNames): array
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

    public function removeExtension($extension)
    {
        $this->removeExtension->remove($extension);
    }
}
