<?php

namespace Phpactor\TextDocument\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\Exception\TextDocumentNotFound;
use Phpactor\TextDocument\TextDocumentBuilder;

class TextDocumentBuilderTest extends TestCase
{
    const EXAMPLE_TEXT = 'hello world';
    const EXAMPLE_URI = 'file:///path/to';

    public function testCreate()
    {
        $doc = TextDocumentBuilder::create(self::EXAMPLE_TEXT)->language('php')->uri(self::EXAMPLE_URI)->build();
        $this->assertEquals(self::EXAMPLE_URI, $doc->uri()->__toString());
        $this->assertEquals(self::EXAMPLE_TEXT, $doc->__toString());
        $this->assertEquals('php', $doc->language());
    }

    public function testFromUri()
    {
        $doc = TextDocumentBuilder::fromUri('file://' . __FILE__)->build();
        $this->assertEquals('file://' . __FILE__, $doc->uri());
        $this->assertEquals(file_get_contents(__FILE__), $doc->__toString());
    }

    public function testExceptionOnNotExists()
    {
        $this->expectException(TextDocumentNotFound::class);
        TextDocumentBuilder::fromUri('file:///no-existy');
    }
}
