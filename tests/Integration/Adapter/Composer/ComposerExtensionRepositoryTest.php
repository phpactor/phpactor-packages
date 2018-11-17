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

        /** @var InstallerService $installer */
        $installer = $this->container()->get('extension_manager.service.installer');
        $installer->addExtension('phpactor/console-extension');
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
        $this->createRepository()->find('symfony/console');
    }

    private function createRepository(): ComposerExtensionRepository
    {
        return $this->container()->get('extension_manager.model.extension_repository');
    }
}
