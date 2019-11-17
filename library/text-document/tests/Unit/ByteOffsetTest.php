<?php

namespace Phpactor\TextDocument\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\Exception\InvalidByteOffset;

class ByteOffsetTest extends TestCase
{
    public function testValue()
    {
        $offset = ByteOffset::fromInt(10);
        $this->assertEquals(10, $offset->toInt());
    }

    public function testExceptionOnLessThanZero()
    {
        $this->expectException(InvalidByteOffset::class);
        ByteOffset::fromInt(-10);
    }
}
