<?php

namespace Phpactor\Extension\ExtensionManager\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Phpactor\Extension\ExtensionManager\Command\ListCommand;
use Phpactor\Extension\ExtensionManager\Model\Extension;
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

    public function testListsInstalledExtensions()
    {
        $this->lister->list()->willReturn([
            new Extension('one', 'dev-xxx', 'One'),
            new Extension('two', 'dev-yyy', 'Two'),
        ]);

        $this->tester->execute([]);
        $this->assertEquals(<<<'EOT'
+------+---------+-------------+
| Name | Version | Description |
+------+---------+-------------+
| one  | dev-xxx | One         |
| two  | dev-yyy | Two         |
+------+---------+-------------+

EOT
        , $this->tester->getDisplay());
    }
}
