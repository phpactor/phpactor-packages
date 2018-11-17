<?php

namespace Phpactor\Extension\CompletionRpc\Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Extension\CompletionRpc\Handler\CompleteHandler;
use Phpactor\Extension\Rpc\Response\ReturnResponse;
use Phpactor\Extension\Rpc\Test\HandlerTester;
use Prophecy\Prophecy\ObjectProphecy;

class CompleteHandlerTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $completor;

    public function setUp()
    {
        $this->completor = $this->prophesize(Completor::class);
    }

    public function testHandler()
    {
        $handler = new CompleteHandler($this->completor->reveal());
        $this->completor->complete('aaa', 1234)->will(function () {
            yield Suggestion::create('aaa');
            yield Suggestion::create('bbb');
        });
        $action = (new HandlerTester($handler))->handle('complete', ['source' => 'aaa', 'offset' => 1234]);

        $this->assertInstanceOf(ReturnResponse::class, $action);
        $this->assertArraySubset([
            [
                'name' => 'aaa',
            ],
            [
                'name' => 'bbb',
            ],
        ], $action->value()['suggestions']);
    }
}
