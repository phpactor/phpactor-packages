<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Rpc;

use Exception;
use PHPUnit\Framework\TestCase;
use Phpactor\Extension\ExtensionManager\Rpc\ExtensionInstallHandler;
use Phpactor\Extension\ExtensionManager\Service\InstallerService;
use Phpactor\Extension\Rpc\Response\CollectionResponse;
use Phpactor\Extension\Rpc\Response\EchoResponse;
use Phpactor\Extension\Rpc\Response\InputCallbackResponse;
use Phpactor\Extension\Rpc\Test\HandlerTester;

class ExtensionInstallHandlerTest extends TestCase
{
    const EXAMPLE_EXTENSION_NAME = 'foo_extension';

    /**
     * @var ObjectProphecy
     */
    private $installer;

    public function setUp()
    {
        $this->installer = $this->prophesize(InstallerService::class);
    }

    public function testAsksForExtensionName()
    {
        $tester = $this->createTester();
        $response = $tester->handle('extension_install', []);
        $this->assertInstanceOf(InputCallbackResponse::class, $response);
    }

    public function testInstallsExtension()
    {
        $tester = $this->createTester();
        $this->installer->requireExtensions([ self::EXAMPLE_EXTENSION_NAME ])->shouldBeCalled();
        $response = $tester->handle('extension_install', [
            'extension_name' => self::EXAMPLE_EXTENSION_NAME,
        ]);
        $this->assertInstanceOf(EchoResponse::class, $response);
    }

    public function testShowsErrorIfExtensionFailedToInstall()
    {
        $tester = $this->createTester();
        $this->installer->requireExtensions([ self::EXAMPLE_EXTENSION_NAME ])->willThrow(new Exception('sorry'));
        $response = $tester->handle('extension_install', [
            'extension_name' => self::EXAMPLE_EXTENSION_NAME,
        ]);
        $this->assertInstanceOf(CollectionResponse::class, $response);
    }

    private function createTester(): HandlerTester
    {
        $tester = new HandlerTester(
            new ExtensionInstallHandler($this->installer->reveal())
        );
        return $tester;
    }
}
