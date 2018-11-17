<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Integration\Adapter\Composer;

use PHPUnit\Framework\TestCase;
use Phpactor\Extension\ExtensionManager\Adapter\Composer\ComposerExtensionRepository;
use Phpactor\Extension\ExtensionManager\ExtensionManagerExtension;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Service\InstallerService;
use Phpactor\Extension\ExtensionManager\Tests\Integration\IntegrationTestCase;
use RuntimeException;

class ComposerExtensionRepositoryTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadProject('Extension', <<<'EOT'
// File: composer.json
{
    "name": "test/extension",
    "type": "phpactor-extension",
    "extra": {
        "phpactor.extension_class": "Foo"
    },
    "require": {
        "test/library": "*"
    }
}
EOT
        );
        $this->loadProject('Library', <<<'EOT'
// File: composer.json
{
    "name": "test/library"
}
EOT
        );

        /** @var InstallerService $installer */
        $installer = $this->container([
            'extension_manager.minimum_stability' => 'dev',
            'extension_manager.repositories' => [
                [
                    'type' => 'path',
                    'url' => $this->workspace->path('Extension'),
                ],
                [
                    'type' => 'path',
                    'url' => $this->workspace->path('Library'),
                ]
            ]
        ])->get('extension_manager.service.installer');
        $installer->addExtension('test/extension');
        $installer->install();
    }

    public function testReturnsAllInstalledExtensions()
    {
        $extensions = $this->createRepository()->extensions();
        $this->assertGreaterThan(0, count($extensions));
        $this->assertContainsOnlyInstancesOf(Extension::class, $extensions);
    }

    public function testThrowsExceptionWhenTryingToGetNonExistingRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find');
        $this->createRepository()->find('not-existing-yeah');
    }

    public function testThrowsExceptionIfPackageIsNotAnExtension()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('it is a "library"');
        $this->createRepository()->find('test/library');
    }

    private function createRepository(): ComposerExtensionRepository
    {
        return $this->container()->get('extension_manager.model.extension_repository');
    }
}
