<?php

namespace Phpactor\FilePathResolver\Tests\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Phpactor\FilePathResolver\Filter;
use Phpactor\FilePathResolver\FilteringPathResolver;

abstract class FilterTestCase extends TestCase
{
    abstract protected function createFilter(): Filter;

    public function apply(string $path): string
    {
        return (new FilteringPathResolver([ $this->createFilter() ]))->resolve($path);
    }
}
