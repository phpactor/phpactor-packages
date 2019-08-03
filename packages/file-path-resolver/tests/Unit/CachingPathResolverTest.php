<?php

namespace Phpactor\FilePathResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\FilePathResolver\CachingPathResolver;
use Phpactor\FilePathResolver\PathResolver;

class CachingPathResolverTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = $this->prophesize(PathResolver::class);
    }

    public function testCachesResult()
    {
        $caching = new CachingPathResolver($this->resolver->reveal());
        $this->resolver->resolve('foo')->willReturn('bar')->shouldBeCalledOnce();

        $caching->resolve('foo');
        $caching->resolve('foo');
        $caching->resolve('foo');
        $this->assertEquals('bar', $caching->resolve('foo'));
    }
}
