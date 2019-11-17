<?php

namespace Phpactor\TextDocument\Util;

use OutOfBoundsException;
use Phpactor\TextDocument\LineCol;
use RuntimeException;

class LineColFromOffset
{
    private const NEWLINE_PATTERN = '\\r\\n|\\n|\\r';

    public function __invoke(string $document, int $byteOffset): LineCol
    {
        $lines = preg_split('{(' . self::NEWLINE_PATTERN . ')}', $document, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (false === $lines) {
            throw new RuntimeException(
                'Failed to preg-split text into lines'
            );
        }

        $start = 0;
        $linesLength = 0;
        $lineNo = 0;
        foreach ($lines as $index => $line) {
            $end = $start + strlen($line);

            if (!preg_match('{^' . self::NEWLINE_PATTERN . '$}', $line)) {
                $lineNo++;
            }

            if ($byteOffset >= $start && $byteOffset < $end) {
                $section = substr($document, $start, $byteOffset - $start);
                return new LineCol($lineNo, mb_strlen($section));
            }

            $start = $end;
            $linesLength += mb_strlen($line);
        }

        throw new OutOfBoundsException(sprintf(
            'Byte offset %s is larger than text length %s',
            $byteOffset,
            strlen($document)
        ));
    }
}
