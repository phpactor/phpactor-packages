<?php

namespace Phpactor\BasePathResolver\Tests\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Phpactor\BasePathResolver\Filter;
use Phpactor\BasePathResolver\PathResolver;

abstract class FilterTestCase extends TestCase
{
    abstract protected function createFilter(): Filter;

    public function apply(string $path): string
    {
        return (new PathResolver([ $this->createFilter() ]))->resolve($path);
    }
}
