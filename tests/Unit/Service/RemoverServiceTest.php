<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Service;

use Phpactor\Extension\ExtensionManager\Model\DependentExtensionFinder;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionConfig;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\ExtensionState;
use Phpactor\Extension\ExtensionManager\Model\Installer;
use Phpactor\Extension\ExtensionManager\Service\RemoverService;
use Phpactor\Extension\ExtensionManager\Tests\TestCase;
use RuntimeException;

class RemoverServiceTest extends TestCase
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
    private $repository;
    /**
     * @var RemoverService
     */
    private $service;
    /**
     * @var ObjectProphecy
     */
    private $extension1;

    public function setUp()
    {
        $this->installer = $this->prophesize(Installer::class);
        $this->config = $this->prophesize(ExtensionConfig::class);
        $this->finder = $this->prophesize(DependentExtensionFinder::class);
        $this->repository = $this->prophesize(ExtensionRepository::class);

        $this->service = new RemoverService(
            $this->installer->reveal(),
            $this->finder->reveal(),
            $this->repository->reveal(),
            $this->config->reveal()
        );

        $this->extension1 = $this->prophesize(Extension::class);
    }

    public function testRemoveExtension()
    {
        $this->repository->find('foobar')->willReturn($this->extension1->reveal());
        $this->extension1->state()->willReturn(new ExtensionState(ExtensionState::STATE_SECONDARY));

        $this->config->unrequire('foobar')->shouldBeCalled();
        $this->config->commit()->shouldBeCalled();
        $this->service->removeExtension('foobar');
    }

    public function testThrowsExceptionIfExtensionIsPrimary()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is a primary');

        $this->repository->find('foobar')->willReturn($this->extension1->reveal());
        $this->extension1->state()->willReturn(new ExtensionState(ExtensionState::STATE_PRIMARY));

        $this->service->removeExtension('foobar');
    }
}
