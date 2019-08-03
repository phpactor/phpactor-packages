<?php

namespace Phpactor\TextDocument\Util;

use OutOfBoundsException;
use RuntimeException;

final class LineAtOffset
{
    public function __invoke(string $text, int $byteOffset): string
    {
        $lines = preg_split("{(\r\n|\n|\r)}", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (false === $lines) {
            throw new RuntimeException(
                'Failed to preg-split text into lines'
            );
        }

        $start = 0;
        foreach ($lines as $line) {
            $end = $start + strlen($line);
            if ($byteOffset >= $start && $byteOffset < $end) {
                return $line;
            }
            $start = $end;
        }

        throw new OutOfBoundsException(sprintf(
            'Byte offset %s is larger than text length %s',
            $byteOffset,
            strlen($text)
        ));
    }
}
