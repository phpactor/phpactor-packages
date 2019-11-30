<?php

namespace Phpactor\TextDocument\Tests\Unit\Util;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\Util\LineColFromOffset;

class LineColFromOffsetTest extends TestCase
{
    /**
     * @dataProvider provideLineColFromOffset
     */
    public function testLineColFromOffset(string $document, int $offset, int $expectedLine, int $expectedCol)
    {
        $lineCol = (new LineColFromOffset())($document, $offset);
        $this->assertEquals($expectedLine, $lineCol->line(), 'line no');
        $this->assertEquals($expectedCol, $lineCol->col(), 'col no');
    }

    public function provideLineColFromOffset()
    {
        yield [
            'hello',
            1,
            1,
            1
        ];

        yield [
            'hello',
            2,
            1,
            2
        ];

        yield 'multiline' => [
            "hello\ngoodbye",
            8,
            2,
            2
        ];

        yield '2 lines with special chars' => [
            "h转llo\ngoodbye",
            10,
            2,
            2
        ];

        yield '4 lines with special chars' => [
            <<<'EOT'
h转llo
goodbye
h转llo
goodbye
EOT
        ,
            26,
            4,
            2
        ];
    }

    public function testExceptionWhenOutOfBounds()
    {
        $this->expectException(OutOfBoundsException::class);
        $lineCol = (new LineColFromOffset())('asd', 10);
    }
}
