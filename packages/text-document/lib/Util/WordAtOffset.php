<?php

namespace Phpactor\TextDocument\Util;

use OutOfBoundsException;
use RuntimeException;

final class WordAtOffset
{
    const SPLIT_WORD = '\s|;|\\\|%|\(|\)|\[|\]|:|\r|\r\n|\n';
    const SPLIT_QUALIFIED_PHP_NAME = '\s|;|%|\(|\)|\[|\]|:|\r|\r\n|\n';

    /**
     * @var string
     */
    private $splitPattern;

    public function __construct(string $splitPattern = self::SPLIT_WORD)
    {
        $this->splitPattern = $splitPattern;
    }

    public function __invoke(string $text, int $byteOffset): string
    {
        $chunks = preg_split('{(' . $this->splitPattern . ')}', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (false === $chunks) {
            throw new RuntimeException(
                'Failed to preg-split text into chunks'
            );
        }

        $start = 0;
        foreach ($chunks as $chunk) {
            $end = $start + strlen($chunk);
            if ($byteOffset >= $start && $byteOffset < $end) {
                return $chunk;
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
