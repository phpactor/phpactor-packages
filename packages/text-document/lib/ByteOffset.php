<?php

namespace Phpactor\TextDocument;

use Phpactor\TextDocument\Exception\InvalidByteOffset;

class ByteOffset
{
    /**
     * @var int
     */
    private $offset;

    private function __construct(int $offset)
    {
        if ($offset < 0) {
            throw new InvalidByteOffset(sprintf(
                'Offset must be greater than or equal to zero, got "%s"',
                $offset
            ));
        }
        $this->offset = $offset;
    }

    public static function fromInt(int $offset): self
    {
        return new self($offset);
    }

    public function toInt(): int
    {
        return $this->offset;
    }
}
