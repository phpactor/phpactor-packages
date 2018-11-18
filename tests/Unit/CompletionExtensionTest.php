<?php

namespace Phpactor\Extension\Completion\Tests\Unit;

use Closure;
use PHPUnit\Framework\TestCase;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Formatter\Formatter;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\MapResolver\Resolver;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

class CompletionExtensionTest extends TestCase
{
    const EXAMPLE_SUGGESTION = 'example_suggestion';
    const EXAMPLE_SOURCE = 'asd';
    const EXAMPLE_OFFSET = 1234;


    /**
     * @var ObjectProphecy
     */
    private $completor1;

    /**
     * @var ObjectProphecy
     */
    private $formatter1;

    public function setUp()
    {
        $this->completor1 = $this->prophesize(Completor::class);
        $this->formatter1 = $this->prophesize(Formatter::class);
    }

    public function testCreatesChainedCompletor()
    {
        $this->completor1->complete(self::EXAMPLE_SOURCE, self::EXAMPLE_OFFSET)->will(function () {
            return (function () {
                yield Suggestion::create(self::EXAMPLE_SUGGESTION);
            })();
        });

        $completor = $this->createContainer()->get(CompletionExtension::SERVICE_REGISTRY)->completorForType('php');
        $results = iterator_to_array($completor->complete(self::EXAMPLE_SOURCE, self::EXAMPLE_OFFSET));

        $this->assertEquals(self::EXAMPLE_SUGGESTION, $results[0]->name());
    }

    public function testCreatesFormatterFromEitherSingleFormatterOrArray()
    {
        $object = new stdClass();
        $this->formatter1->canFormat($object)->shouldBeCalledTimes(3)->willReturn(false);

        $formatter = $this->createContainer()->get(CompletionExtension::SERVICE_FORMATTER);
        $canFormat = $formatter->canFormat($object);
        $this->assertEquals(false, $canFormat);
    }

    private function createContainer(): Container
    {
        $builder = new PhpactorContainer();
        $extension = new CompletionExtension();

        $builder->register('completor1', function () {
            return $this->completor1->reveal();
        }, [ CompletionExtension::TAG_COMPLETOR => []]);

        $builder->register('formatter', function () {
            return $this->formatter1->reveal();
        }, [ CompletionExtension::TAG_FORMATTER => []]);

        $builder->register('formatter_array', function () {
            return [
                $this->formatter1->reveal(),
                $this->formatter1->reveal(),
            ];
        }, [ CompletionExtension::TAG_FORMATTER => []]);
        
        $extension->load($builder);
        return $builder->build([]);
    }
}
