<?php

namespace Phpactor\TextDocument;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;

class Locations implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    private $locations = [];

    public function __construct(array $locations)
    {
        foreach ($locations as $location) {
            $this->add($location);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->locations);
    }

    private function add(Location $location)
    {
        $this->locations[] = $location;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->locations);
    }

    public function first(): Location
    {
        if (count($this->locations) === 0) {
            throw new RuntimeException(
                'There are no locations in this collection'
            );
        }

        return reset($this->locations);
    }
}
