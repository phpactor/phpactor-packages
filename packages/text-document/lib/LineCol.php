<?php

namespace Phpactor\TextDocument;

class LineCol
{
    /**
     * @var int
     */
    private $line;

    /**
     * @var int
     */
    private $col;

    public function __construct(int $line, int $col)
    {
        $this->line = $line;
        $this->col = $col;
    }

    public function col(): int
    {
        return $this->col;
    }

    public function line(): int
    {
        return $this->line;
    }
}
