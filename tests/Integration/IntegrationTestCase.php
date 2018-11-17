<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Integration;

use Phpactor\Container\Container;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\ExtensionManager\ExtensionManagerExtension;
use Phpactor\Extension\ExtensionManager\Tests\TestCase;

class IntegrationTestCase extends TestCase
{
    protected function container(): Container
    {
        return PhpactorContainer::fromExtensions([
            ExtensionManagerExtension::class,
            ConsoleExtension::class,
        ], [
            ExtensionManagerExtension::PARAM_VENDOR_DIR => $this->workspace->path('vendordor'),
            ExtensionManagerExtension::PARAM_EXTENSION_VENDOR_DIR => $this->workspace->path('vendordor-ext'),
            ExtensionManagerExtension::PARAM_EXTENSION_CONFIG_FILE => $this->workspace->path('extension.json'),
            ExtensionManagerExtension::PARAM_INSTALLED_EXTENSIONS_FILE => $this->workspace->path('installer.php'),
        ]);
    }
}
