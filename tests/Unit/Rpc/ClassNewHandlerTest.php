<?php

namespace Phpactor\Extension\CodeTransform\Tests\Unit\Rpc;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\CodeTransform\Domain\GenerateFromExisting;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\Extension\CodeTransform\Rpc\ClassInflectHandler;
use Phpactor\Extension\CodeTransform\Rpc\ClassNewHandler;
use Phpactor\Extension\Rpc\Handler;
use Phpactor\Extension\Rpc\Response\InputCallbackResponse;
use Phpactor\Extension\Rpc\Response\Input\ChoiceInput;
use Phpactor\Extension\Rpc\Response\Input\TextInput;
use Phpactor\Extension\Rpc\Test\HandlerTester;
use Prophecy\Prophecy\ObjectProphecy;

class ClassNewHandlerTest extends AbstractClassGenerateHandlerTest
{
    /**
     * @var ObjectProphecy
     */
    private $generator;

    public function setUp()
    {
        parent::setUp();
        $this->generator = $this->prophesize(GenerateFromExisting::class);
    }

    public function createHandler(): Handler
    {
        return new ClassNewHandler(
            new Generators([
                'one' => $this->generator->reveal()
            ]),
            $this->fileToClass->reveal()
        );
    }
}
