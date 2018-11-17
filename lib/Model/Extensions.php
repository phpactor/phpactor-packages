<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Extensions implements IteratorAggregate, Countable
{
    private $extensions = [];

    public function __construct(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->add($extension);
        }
    }

    public function merge(Extensions $extensions): Extensions
    {
        return new Extensions(array_merge($this->extensions, $extensions->extensions));
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->extensions);
    }

    private function add(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->extensions);
    }

    public function names(): array
    {
        return array_map(function (Extension $extension) {
            return $extension->name();
        }, $this->extensions);
    }

    public function primaries(): Extensions
    {
        return new Extensions(array_filter($this->extensions, function (Extension $extension) {
            return $extension->isPrimary();
        }));
    }
}
