<?php

namespace Phpactor\Extension\Console\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\Console\Tests\Unit\Example\InvalidExtension;
use Phpactor\Extension\Console\Tests\Unit\Example\TestExtension;
use Symfony\Component\Console\Command\Command;

class ConsoleExtensionTest extends TestCase
{
    public function testCreatesCommandLoader()
    {
        $container = PhpactorContainer::fromExtensions([
            ConsoleExtension::class,
            TestExtension::class
        ]);

        $loader = $container->get(ConsoleExtension::SERVICE_COMMAND_LOADER);
        $command = $loader->get('test');

        $this->assertInstanceOf(Command::class, $command);
    }

    public function testThrowsExceptionIfNoNameAttributeProvided()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must have the "name" attribute');
        $container = PhpactorContainer::fromExtensions([
            ConsoleExtension::class,
            InvalidExtension::class
        ]);

        $loader = $container->get(ConsoleExtension::SERVICE_COMMAND_LOADER);
    }
}
