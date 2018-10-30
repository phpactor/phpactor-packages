<?php

namespace Phpactor\BasePathResolver\Tests\Unit\Filter;

use Phpactor\BasePathResolver\Filter;
use Phpactor\BasePathResolver\Filter\CanonicalizingPathFilter;

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
