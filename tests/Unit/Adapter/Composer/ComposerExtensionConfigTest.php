<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Adapter\Composer;

use Phpactor\Extension\ExtensionManager\Adapter\Composer\ComposerExtensionConfig;
use Phpactor\Extension\ExtensionManager\Tests\TestCase;
use RuntimeException;

class ComposerExtensionConfigTest extends TestCase
{
    const EXAMPLE_PATH = 'extension.json';

    /**
     * @var ComposerExtensionConfig
     */
    private $config;

    /**
     * @var string
     */
    private $path;

    public function setUp()
    {
        parent::setUp();
        $this->path = $this->workspace->path(self::EXAMPLE_PATH);
        file_put_contents($this->path, '{}');
        $this->config = new ComposerExtensionConfig($this->path);
    }

    public function testThrowsExceptionifConfigFileDoesNotExist()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not exist');
        new ComposerExtensionConfig(__DIR__ . '/no');
    }

    public function testThrowsExceptionWithInvalidJson()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON');
        file_put_contents($this->path, 'asd');
        new ComposerExtensionConfig($this->path);
    }

    public function testRequires()
    {
        $this->config->require('foo', 'bar');
        $this->config->commit();
        $this->assertEquals([
            'require' => [
                'foo' => 'bar'
            ]
        ], $this->render());
    }

    public function testUnrequireRemovesRequireElementCompletely()
    {
        $this->config->require('foo', 'bar');
        $this->config->commit();

        $this->config->unrequire('foo');
        $this->config->commit();

        $this->assertEquals([
        ], $this->render());
    }

    private function render(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }
}
