<?php

namespace spec\Phpactor\Extension\PhpSpec\Tests\Example;

use Phpactor\Extension\PhpSpec\Tests\Example\Example;
use PhpSpec\ObjectBehavior;
use Phpactor\Extension\PhpSpec\Tests\Example\Goodbye;
use Prophecy\Argument;

class ExampleSpec extends ObjectBehavior
{
    function it_is_initializable(Goodbye $bar)
    {
    }
}
