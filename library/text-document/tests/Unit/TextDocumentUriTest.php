<?php

namespace Phpactor\TextDocument\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\Exception\InvalidUriException;
use Phpactor\TextDocument\TextDocumentUri;

class TextDocumentUriTest extends TestCase
{
    public function testCreate()
    {
        $uri = TextDocumentUri::fromString('file://' . __FILE__);
        $this->assertEquals('file://' . __FILE__, (string) $uri);
    }

    public function testNormalizesToFileScheme()
    {
        $uri = TextDocumentUri::fromString(__FILE__);
        $this->assertEquals('file://' . __FILE__, (string) $uri);
    }

    public function testExceptionOnNonAbsolutePath()
    {
        $this->expectException(InvalidUriException::class);
        TextDocumentUri::fromString('i is relative');
    }

    public function testExceptionOnNoPath()
    {
        $this->expectException(InvalidUriException::class);
        $this->expectExceptionMessage('has no path');
        TextDocumentUri::fromString('file://');
    }

    public function testReturnsPath()
    {
        $uri = TextDocumentUri::fromString('file://' . __FILE__);
        $this->assertEquals(__FILE__, $uri->path());
    }

    public function testScheme()
    {
        $uri = TextDocumentUri::fromString('file://' . __FILE__);
        $this->assertEquals('file', $uri->scheme());
    }
}
