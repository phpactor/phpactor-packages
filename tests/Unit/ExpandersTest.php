<?php

namespace Phpactor\FilePathResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\FilePathResolver\Expander\ValueExpander;
use Phpactor\FilePathResolver\Expanders;

class ExpandersTest extends TestCase
{
    public function testProvidesArrayRepresentation()
    {
        $expanders = new Expanders([
            new ValueExpander('foo', 'bar'),
            new ValueExpander('bar', 'foo'),
        ]);

        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'foo',
        ], $expanders->toArray());
    }
}
