<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Integration;

use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\ExtensionManager\ExtensionManagerExtension;
use Phpactor\Extension\ExtensionManager\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    public function testInstall()
    {
        [$exit, $out] = $this->runCommand([
            'command' => 'extension:install'
        ]);
        $this->assertEquals(0, $exit);
    }

    public function testRemove()
    {
        [$exit, $out] = $this->runCommand([
            'command' => 'extension:install',
            'extension' =>  'phpactor/logging-extension'
        ]);
        $this->assertEquals(0, $exit);

        [$exit, $out] = $this->runCommand([
            'command' => 'extension:remove',
            'extension' =>  [ 'phpactor/logging-extension' ],
        ]);
        $this->assertEquals(0, $exit);
    }

    public function testList()
    {
        [$exit, $out] = $this->runCommand([
            'command' => 'extension:install',
            'extension' =>  'phpactor/logging-extension'
        ]);
        $this->assertEquals(0, $exit);

        [$exit, $out] = $this->runCommand([
            'command' => 'extension:list',
        ]);

        $this->assertContains('logging-extension', $out);
        $this->assertEquals(0, $exit);
    }

    public function testUpdate()
    {
        [$exit, $out] = $this->runCommand([
            'command' => 'extension:update',
        ]);
        $this->assertEquals(0, $exit);
    }

    private function runCommand(array $params): array
    {
        $container = PhpactorContainer::fromExtensions([
            ExtensionManagerExtension::class,
            ConsoleExtension::class,
        ], [
            ExtensionManagerExtension::PARAM_VENDOR_DIR => $this->workspace->path('vendordor'),
            ExtensionManagerExtension::PARAM_EXTENSION_VENDOR_DIR => $this->workspace->path('vendordor-ext'),
            ExtensionManagerExtension::PARAM_EXTENSION_CONFIG_FILE => $this->workspace->path('extension.json'),
            ExtensionManagerExtension::PARAM_INSTALLED_EXTENSIONS_FILE => $this->workspace->path('installer.php'),
        ]);
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCommandLoader(
            $container->get(ConsoleExtension::SERVICE_COMMAND_LOADER)
        );
        $output = new BufferedOutput();
        $exit = $application->run(new ArrayInput($params), $output);

        return [$exit, $output->fetch()];
    }
}
