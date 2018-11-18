<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Phpactor\Extension\ExtensionManager\Command\ListCommand;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\Extensions;
use Phpactor\Extension\ExtensionManager\Service\ExtensionLister;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var ObjectProphecy
     */
    private $lister;

    public function setUp()
    {
        $this->lister = $this->prophesize(ExtensionLister::class);
        $this->tester = new CommandTester(new ListCommand($this->lister->reveal()));
    }

    public function testListsAllExtensions()
    {
        $this->lister->list(false)->willReturn(new Extensions([
            new Extension('one', 'dev-xxx', 'One'),
            new Extension('two', 'dev-yyy', 'Two'),
        ]));

        $this->tester->execute([]);
        $this->assertContains(<<<'EOT'
one  | dev-xxx | One 
EOT
        , $this->tester->getDisplay());
    }

    public function testListsInstalledExtensions()
    {
        $this->lister->list(true)->willReturn(new Extensions([
            new Extension('one', 'dev-xxx', 'One'),
            new Extension('two', 'dev-yyy', 'Two'),
        ]));

        $this->tester->execute([
            '--installed' => true,
        ]);
        $this->assertEquals(0, $this->tester->getStatusCode());
    }
}
