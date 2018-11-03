<?php

namespace Phpactor\Extension\CompletionRpc\Tests\Handler;

use PHPUnit\Framework\TestCase;
use Phpactor\Completion\Core\Formatter\ObjectFormatter;
use Phpactor\Extension\CompletionRpc\Handler\HoverHandler;
use Phpactor\Extension\Rpc\Test\HandlerTester;
use Phpactor\TestUtils\ExtractOffset;
use Phpactor\WorseReflection\ReflectorBuilder;

class HoverHandlerTest extends TestCase
{
    /**
     * @var Reflector
     */
    private $reflector;

    public function setUp()
    {
        $this->reflector = ReflectorBuilder::create()->enableContextualSourceLocation()->build();
        $this->formatter = new ObjectFormatter([]);
    }

    /**
     * @dataProvider provideHover
     */
    public function testHover(string $source, string $expectedMessage)
    {
        [ $source, $offset ] = ExtractOffset::fromSource($source);
 
        $response = (new HandlerTester(
            new HoverHandler($this->reflector, $this->formatter)
        ))->handle(
            HoverHandler::NAME,
            [
            'source' => $source,
            'offset' => $offset,
        ]
        );

        $this->assertEquals($expectedMessage, $response->message());
    }

    public function provideHover()
    {
        yield 'method' => [
            '<?php class Foobar { public function fo<>obar() { } }',
            'method foobar',
        ];

        yield 'property' => [
            '<?php class Foobar { private $fo<>obar; }',
            'property foobar',
        ];

        yield 'constant' => [
            '<?php class Foobar { const fo<>obar = 123; }',
            'constant foobar',
        ];

        yield 'class' => [
            '<?php c<>lass Foobar {}',
            'class Foobar',
        ];

        yield 'variable' => [
            '<?php $f<>oo = "bar"',
            'variable foo',
        ];

        yield 'unknown' => [
            '<?php <> $foo = "bar"',
            '<unknown> <unknown>',
        ];
    }
}
