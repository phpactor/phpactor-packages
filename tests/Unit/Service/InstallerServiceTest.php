<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Service;

use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Model\VersionFinder;
use Phpactor\Extension\ExtensionManager\Service\InstallerService;
use Phpactor\Extension\ExtensionManager\Tests\TestCase;

class InstallerServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $installer;

    /**
     * @var ObjectProphecy
     */
    private $config;

    /**
     * @var ObjectProphecy
     */
    private $finder;

    /**
     * @var ObjectProphecy
     */
    private $extension1;

    /**
     * @var InstallerService
     */
    private $service;

    public function setUp()
    {
        $this->installer = $this->prophesize(Installer::class);
        $this->config = $this->prophesize(ExtensionConfig::class);
        $this->finder = $this->prophesize(VersionFinder::class);
        $this->repository = $this->prophesize(ExtensionRepository::class);

        $this->service = new InstallerService(
            $this->installer->reveal(),
            $this->config->reveal(),
            $this->finder->reveal(),
            $this->repository->reveal()
        );

        $this->extension1 = $this->prophesize(Extension::class);
    }

    public function testAddExtension()
    {
        $this->finder->findBestVersion('foobar')->willReturn('dev-foo');
        $this->config->require('foobar', 'dev-foo')->shouldBeCalled();
        $this->config->commit()->shouldBeCalled();

        $this->service->addExtension('foobar');
    }
}
