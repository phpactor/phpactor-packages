<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Installer as ComposerInstaller;
use Phpactor\Container\Container;
use Phpactor\Extension\ExtensionManager\Model\Installer;

class LazyComposerInstaller implements Installer
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function install(): void
    {
        $this->installer()->run();
    }

    public function installForceUpdate(): void
    {
        $installer = $this->installer();
        $installer->setUpdate(true);
        $installer->run();
    }

    private function installer(): ComposerInstaller
    {
        $installer = $this->container->get('extension_manager.installer');
        return $installer;
    }
}
