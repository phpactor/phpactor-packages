<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Model\Installer;

class InstallerService
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var AddExtension
     */
    private $addExtension;

    public function __construct(Installer $installer, AddExtension $addExtension)
    {
        $this->installer = $installer;
        $this->addExtension = $addExtension;
    }

    public function addExtension($extension): void
    {
        $this->addExtension->add($extension);
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
