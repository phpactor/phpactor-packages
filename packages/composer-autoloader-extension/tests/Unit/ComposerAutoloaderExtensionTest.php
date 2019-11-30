<?php

namespace Phpactor\Extension\ComposerAutoloader\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\Container\Container;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Composer\Autoload\ClassLoader;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use RuntimeException;

class ComposerAutoloaderExtensionTest extends TestCase
{
    public function testProvidesAutoloaders()
    {
        $autoloaders = $this->create([
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);
        $this->assertCount(1, $autoloaders);
        $autoloader = reset($autoloaders);
        $this->assertInstanceOf(ClassLoader::class, $autoloader);
    }

    public function testProvidesAutoloadersNoDeregister()
    {
        $autoloaders = $this->create([
            ComposerAutoloaderExtension::PARAM_AUTOLOAD_DEREGISTER => false,
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);
        $this->assertCount(1, $autoloaders);
        $autoloader = reset($autoloaders);
        $this->assertInstanceOf(ClassLoader::class, $autoloader);
    }

    public function testWithCustomProjectRoot()
    {
        $autoloaders = $this->create([
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);
        $this->assertCount(1, $autoloaders);
        $autoloader = reset($autoloaders);
        $this->assertInstanceOf(ClassLoader::class, $autoloader);
    }

    public function testWarningForNonExistingLoader()
    {
        $autoloaders = $this->create([
            ComposerAutoloaderExtension::PARAM_AUTOLOADER_PATH => 'not-existing.php',
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);

        $this->assertCount(0, $autoloaders);
    }

    public function testWhenAutoloaderIsntAutoloader()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Autoloader is not');
        $autoloaders = $this->create([
            ComposerAutoloaderExtension::PARAM_AUTOLOADER_PATH => __DIR__ . '/not-an-autoloader.php',
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);
    }

    public function testMultipleAutoloaders()
    {
        $autoloaders = $this->create([
            ComposerAutoloaderExtension::PARAM_AUTOLOADER_PATH => [
                __DIR__ . '/../../vendor/autoload.php',
                __DIR__ . '/../../vendor/autoload.php',
            ],
        ])->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS);
        $this->assertCount(2, $autoloaders);
    }

    private function create(array $config): Container
    {
        return PhpactorContainer::fromExtensions([
            ComposerAutoloaderExtension::class,
            LoggingExtension::class,
            FilePathResolverExtension::class
        ], $config);
    }
}
