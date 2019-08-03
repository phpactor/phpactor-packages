<?php

namespace Phpactor\Extension\ReferenceFinder\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\Extension\ReferenceFinder\Tests\Example\SomeDefinitionLocator;
use Phpactor\Extension\ReferenceFinder\Tests\Example\SomeExtension;
use Phpactor\ReferenceFinder\ChainDefinitionLocationProvider;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocumentBuilder;

class ReferenceFinderExtensionTest extends TestCase
{
    public function testEmptyChainProvider()
    {
        $container = PhpactorContainer::fromExtensions([
            ReferenceFinderExtension::class,
            LoggingExtension::class,
        ]);

        $locator = $container->get(ReferenceFinderExtension::SERVICE_DEFINITION_LOCATOR);
        $this->assertInstanceOf(ChainDefinitionLocationProvider::class, $locator);
    }

    public function testEmptyChainProviderWithRegisteredLocators()
    {
        $container = PhpactorContainer::fromExtensions([
            ReferenceFinderExtension::class,
            SomeExtension::class,
            LoggingExtension::class,
        ]);

        $locator = $container->get(ReferenceFinderExtension::SERVICE_DEFINITION_LOCATOR);
        $this->assertInstanceOf(ChainDefinitionLocationProvider::class, $locator);

        $location = $locator->locateDefinition(TextDocumentBuilder::create('asd')->build(), ByteOffset::fromInt(1));
        $this->assertEquals(SomeDefinitionLocator::EXAMPLE_OFFSET, $location->offset()->toInt());
        $this->assertEquals(SomeDefinitionLocator::EXAMPLE_PATH, $location->uri()->path());
    }
}
