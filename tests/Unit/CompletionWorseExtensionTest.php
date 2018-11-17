<?php

namespace Phpactor\Extension\CompletionWorse\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\Completion\Core\Completor;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\ClassToFile\ClassToFileExtension;
use Phpactor\Extension\CompletionWorse\CompletionWorseExtension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Phpactor\Extension\SourceCodeFilesystem\SourceCodeFilesystemExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;

class CompletionWorseExtensionTest extends TestCase
{
    public function testBuild()
    {
        $container = PhpactorContainer::fromExtensions([
            CompletionExtension::class,
            FilePathResolverExtension::class,
            ClassToFileExtension::class,
            ComposerAutoloaderExtension::class,
            LoggingExtension::class,
            WorseReflectionExtension::class,
            CompletionWorseExtension::class,
            SourceCodeFilesystemExtension::class
        ]);

        $completor = $container->get(CompletionExtension::SERVICE_COMPLETOR);
        $this->assertInstanceOf(Completor::class, $completor);
        assert($completor instanceof Completor);
        $completor->complete('<?php array', 10);
    }
}
