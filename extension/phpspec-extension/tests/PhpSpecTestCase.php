<?php

namespace Phpactor\Extension\PhpSpec\Tests;

use PHPUnit\Framework\TestCase;
use Phpactor\Container\Container;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\ClassToFile\ClassToFileExtension;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\PhpSpec\PhpSpecExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;

class PhpSpecTestCase extends TestCase
{
    public function container(array $parameters = []): Container
    {
        $parameters[FilePathResolverExtension::PARAM_APPLICATION_ROOT] = __DIR__ .'/..';
        $parameters[ComposerAutoloaderExtension::PARAM_AUTOLOADER_PATH] = __DIR__ . '/../vendor/autoload.php';
        $parameters[LoggingExtension::PARAM_ENABLED] = true;
        return PhpactorContainer::fromExtensions([
            WorseReflectionExtension::class,
            LoggingExtension::class,
            ComposerAutoloaderExtension::class,
            PhpSpecExtension::class,
            ClassToFileExtension::class,
            FilePathResolverExtension::class
        ], $parameters);
    }
}
