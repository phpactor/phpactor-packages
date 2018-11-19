<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Integration\Model;

use Composer\Package\CompletePackageInterface;
use PHPUnit\Framework\TestCase;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionFileGenerator;
use Phpactor\Extension\ExtensionManager\Model\Extensions;
use Phpactor\Extension\ExtensionManager\Tests\Integration\IntegrationTestCase;
use Prophecy\Prophecy\ObjectProphecy;

class ExtensionFileGeneratorTest extends IntegrationTestCase
{
    const EXAMPLE_CLASS_NAME = 'Foo\\Bar';

    /**
     * @var ObjectProphecy
     */
    private $package;

    /**
     * @var ExtensionFileGenerator
     */
    private $generator;

    /**
     * @var string
     */
    private $path;

    /**
     * @var ObjectProphecy
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->package = $this->prophesize(CompletePackageInterface::class);
        $this->path = $this->workspace->path('extensions.php');
        $this->generator = new ExtensionFileGenerator($this->path);

        $this->extension = $this->prophesize(Extension::class);
        $this->extension->name()->willReturn('test_extension');
    }

    public function testGenerate()
    {
        $this->extension->className()->willReturn(self::EXAMPLE_CLASS_NAME);
        $this->generator->writeExtensionList(new Extensions([
            $this->extension->reveal()
        ]));

        $extensions = require($this->path);
        $this->assertEquals([
            '\\' . self::EXAMPLE_CLASS_NAME
        ], $extensions);
    }

    public function testGeneratesNonExistingDirectory()
    {
        $path = $this->workspace->path('Foo/Foobar/Bar/extensions.php');
        $generator = new ExtensionFileGenerator($path);
        $generator->writeExtensionList(new Extensions([]));
        $this->assertFileExists($path);
    }
}
