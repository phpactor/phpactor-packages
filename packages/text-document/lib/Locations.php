<?php

namespace Phpactor\TextDocument;

use ArrayIterator;
use IteratorAggregate;

class Locations implements IteratorAggregate
{
    /**
     * @var array
     */
    private $locations;

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
}
