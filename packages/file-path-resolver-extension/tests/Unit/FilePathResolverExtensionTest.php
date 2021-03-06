<?php

namespace Phpactor\FilePathResolverExtension\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\FilePathResolver\PathResolver;

class FilePathResolverExtensionTest extends TestCase
{
    public function testPathResolver()
    {
        $resolver = $this->createResolver([
        ]);

        $this->assertContains('cache/phpactor', $resolver->resolve('%cache%'));
        $this->assertContains('config/phpactor', $resolver->resolve('%config%'));
        $this->assertContains('/phpactor', $resolver->resolve('%data%'));
        $this->assertContains(getcwd(), $resolver->resolve('%project_root%'));
    }

    public function testPathResolverWithApplicationRoot()
    {
        $resolver = $this->createResolver([
            FilePathResolverExtension::PARAM_APPLICATION_ROOT => __DIR__,
        ]);

        $this->assertEquals(__DIR__, $resolver->resolve('%application_root%'));
    }

    public function createResolver(array $config): PathResolver
    {
        $container = PhpactorContainer::fromExtensions([
            FilePathResolverExtension::class,
            LoggingExtension::class
        ], $config);

        return $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER);
    }
}
