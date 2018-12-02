<?php

namespace Phpactor\Extension\CodeTransform\Tests\Unit\Rpc;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\ClassName as ConvertedClassName;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\CodeTransform\Domain\ClassName;
use Phpactor\CodeTransform\Domain\GenerateFromExisting;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\CodeTransform\Domain\SourceCode;
use Phpactor\Extension\CodeTransform\Rpc\ClassInflectHandler;
use Phpactor\Extension\Rpc\Handler;
use Phpactor\Extension\Rpc\Response\InputCallbackResponse;
use Phpactor\Extension\Rpc\Response\Input\ChoiceInput;
use Phpactor\Extension\Rpc\Response\Input\TextInput;
use Phpactor\Extension\Rpc\Response\ReplaceFileSourceResponse;
use Phpactor\Extension\Rpc\Test\HandlerTester;
use Prophecy\Prophecy\ObjectProphecy;

class ClassInflectHandlerTest extends AbstractClassGenerateHandlerTest
{
    const EXAMPLE_NEW_PATH = '/new/path.php';
    const EXAMPLE_VARIANT = 'one';
    const EXAMPLE_CLASS_1 = 'exampleClassName1';
    const EXAMPLE_CLASS_2 = 'exampleClassName2';


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
        return new ClassInflectHandler(
            new Generators([
                self::EXAMPLE_VARIANT => $this->generator->reveal()
            ]),
            $this->fileToClass->reveal()
        );
    }

    public function testInflectsClass()
    {
        $this->fileToClass->fileToClassCandidates(
            FilePath::fromString(self::EXAMPLE_PATH)
        )->willReturn(ClassNameCandidates::fromClassNames([
            $class1 = ConvertedClassName::fromString(self::EXAMPLE_CLASS_1)
        ]));

        $this->fileToClass->fileToClassCandidates(
            FilePath::fromString(self::EXAMPLE_NEW_PATH)
        )->willReturn(ClassNameCandidates::fromClassNames([
            $class2 = ConvertedClassName::fromString(self::EXAMPLE_CLASS_2)
        ]));

        $this->generator->generateFromExisting(
            ClassName::fromString(self::EXAMPLE_CLASS_1),
            ClassName::fromString(self::EXAMPLE_CLASS_2)
        )->willReturn(SourceCode::fromStringAndPath('<?php', self::EXAMPLE_NEW_PATH));
            
        $response = $this->createTester()->handle(ClassInflectHandler::NAME, [
            ClassInflectHandler::PARAM_CURRENT_PATH => self::EXAMPLE_PATH,
            ClassInflectHandler::PARAM_NEW_PATH => self::EXAMPLE_NEW_PATH,
            ClassInflectHandler::PARAM_VARIANT => self::EXAMPLE_VARIANT,
        ]);

        $this->assertInstanceOf(ReplaceFileSourceResponse::class, $response);
        $this->assertEquals(self::EXAMPLE_NEW_PATH, $response->path());
    }
}
