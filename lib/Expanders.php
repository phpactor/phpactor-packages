<?php

namespace Phpactor\FilePathResolver;

use ArrayIterator;
use IteratorAggregate;

class Expanders implements IteratorAggregate
{
    /**
     * @var Expander[]
     */
    private $expanders = [];

    public function toArray(): array
    {
        $array = [];
        foreach ($this->expanders as $expander) {
            $array[$expander->tokenName()] = $expander->replacementValue();
        }

        return $array;
    }

    public function __construct(array $expanders)
    {
        foreach ($expanders as $expander) {
            $this->add($expander);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->expanders);
    }

    private function add(Expander $expander)
    {
        $this->expanders[$expander->tokenName()] = $expander;
    }
}
