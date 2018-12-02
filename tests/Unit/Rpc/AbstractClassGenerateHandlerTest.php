<?php

namespace Phpactor\Extension\CodeTransform\Tests\Unit\Rpc;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\CodeTransform\Domain\GenerateFromExisting;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\Extension\CodeTransform\Rpc\ClassInflectHandler;
use Phpactor\Extension\Rpc\Handler;
use Phpactor\Extension\Rpc\Response\InputCallbackResponse;
use Phpactor\Extension\Rpc\Response\Input\ChoiceInput;
use Phpactor\Extension\Rpc\Response\Input\TextInput;
use Phpactor\Extension\Rpc\Test\HandlerTester;
use Prophecy\Prophecy\ObjectProphecy;

abstract class AbstractClassGenerateHandlerTest extends TestCase
{
    const EXAMPLE_PATH = '/path/to.php';

    /**
     * @var ObjectProphecy
     */
     protected $fileToClass;

    public function setUp()
    {
        $this->fileToClass = $this->prophesize(FileToClass::class);
    }

    public function testAsksForVariant()
    {
        $response = $this->createTester()->handle($this->createHandler()->name(), [
            ClassInflectHandler::PARAM_CURRENT_PATH => self::EXAMPLE_PATH
        ]);

        $this->assertInstanceOf(InputCallbackResponse::class, $response);
        $this->assertCount(2, $response->inputs());
        $input = $response->inputs()[0];
        $this->assertInstanceOf(ChoiceInput::class, $input);

        $input = $response->inputs()[1];
        $this->assertInstanceOf(TextInput::class, $input);
    }

    abstract public function createHandler(): Handler;

    protected function createTester(): HandlerTester
    {
        return new HandlerTester($this->createHandler());
    }
}
