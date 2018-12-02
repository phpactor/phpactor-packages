<?php

namespace Phpactor\Extension\CodeTransform\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\ClassToFileConverter;
use Phpactor\CodeTransform\CodeTransform;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\Container\Container;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\ClassToFile\ClassToFileExtension;
use Phpactor\Extension\CodeTransform\CodeTransformExtension;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Phpactor\Extension\Rpc\Handler;
use Phpactor\Extension\Rpc\RpcExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;

class CodeTransformExtensionTest extends TestCase
{
    public function testLoadServices()
    {
        $container = $this->createContainer();

        $generators = $container->get(CodeTransformExtension::SERVICE_CLASS_GENERATORS);
        $this->assertInstanceOf(Generators::class, $generators);

        $generators = $container->get(CodeTransformExtension::SERVICE_CLASS_INFLECTORS);
        $this->assertInstanceOf(Generators::class, $generators);

        $generators = $container->get(CodeTransformExtension::SERVICE_CODE_TRANSFORM);
        $this->assertInstanceOf(CodeTransform::class, $generators);
    }

    public function testRpcHandlers()
    {
        $container = $this->createContainer();

        foreach ($container->getServiceIdsForTag(RpcExtension::TAG_RPC_HANDLER) as $serviceId => $attrs) {
            $handler = $container->get($serviceId);
            $this->assertInstanceOf(Handler::class, $handler);
        }
    }

    private function createContainer(): Container
    {
        $container = PhpactorContainer::fromExtensions([
            CodeTransformExtension::class,
            ClassToFileExtension::class,
            ComposerAutoloaderExtension::class,
            FilePathResolverExtension::class
        ]);

        return $container;
    }
}
