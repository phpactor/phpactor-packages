<?php

namespace Phpactor\FilePathResolver\Tests\Unit\Expander\Xdg;

use Phpactor\FilePathResolver\Expander;
use Phpactor\FilePathResolver\Expander\Xdg\SuffixExpanderDecorator;
use Phpactor\FilePathResolver\Tests\Unit\Expander\ExpanderTestCase;

class SuffixExpanderDecoratorTest extends ExpanderTestCase
{
    /**
     * @var ObjectProphecy
     */
    private $expander;

    public function setUp()
    {
        $this->expander = $this->prophesize(Expander::class);
    }

    public function createExpander(): Expander
    {
        return new SuffixExpanderDecorator($this->expander->reveal(), '/foo');
    }

    public function testAddsSuffixToInnerExpanderValue()
    {
        $this->expander->tokenName()->willReturn('bar');
        $this->expander->replacementValue()->willReturn('bar');
        $this->assertEquals('/bar/foo', $this->expand('/%bar%'));
    }
}
