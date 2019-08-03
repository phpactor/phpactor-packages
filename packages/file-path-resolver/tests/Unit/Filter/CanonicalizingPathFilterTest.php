<?php

namespace Phpactor\FilePathResolver\Tests\Unit\Filter;

use Phpactor\FilePathResolver\Filter;
use Phpactor\FilePathResolver\Filter\CanonicalizingPathFilter;

class CanonicalizingPathFilterTest extends FilterTestCase
{
    protected function createFilter(): Filter
    {
        return new CanonicalizingPathFilter();
    }

    public function testCanonicalizesThePath()
    {
        $this->assertEquals('/bar', $this->apply('/foo/bar/../../bar'));
    }
}
