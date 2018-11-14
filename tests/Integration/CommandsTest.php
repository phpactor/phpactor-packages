<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\ExtensionManager\ExtensionManagerExtension;
use Phpactor\TestUtils\Workspace;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    /**
     * @var Workspace
     */
    private $workspace;

    public function setUp()
    {
        $this->workspace = Workspace::create(__DIR__ . '/../Workspace');
        $this->workspace->reset();
    }

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
            'command' => 'extension:remove',
            'extension' =>  [ 'phpactor/not-exist-extension' ],
        ]);
        $this->assertContains('Could not find', $out->fetch());
        $this->assertEquals(1, $exit);
    }

    public function testList()
    {
        [$exit, $out] = $this->runCommand([
            'command' => 'extension:list',
        ]);
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

        return [$exit, $output];
    }
}
