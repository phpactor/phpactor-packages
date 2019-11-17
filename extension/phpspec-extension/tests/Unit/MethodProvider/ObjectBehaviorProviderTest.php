<?php

namespace Phpactor\Extension\PhpSpec\Tests\Unit\MethodProvider;

use PhpSpec\Wrapper\Subject;
use Phpactor\Extension\PhpSpec\Tests\PhpSpecTestCase;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\WorseReflection\Core\Reflector\ClassReflector;
use spec\Phpactor\Extension\PhpSpec\Tests\Example\ExampleSpec;

class ObjectBehaviorProviderTest extends PhpSpecTestCase
{
    public function testProvideSpecMethods()
    {
        $container = $this->container();
        $reflector = $container->get(WorseReflectionExtension::SERVICE_REFLECTOR);
        $this->assertInstanceOf(ClassReflector::class, $reflector);

        $class = $reflector->reflectClass(ExampleSpec::class);

        $method = $class->methods()->get('hello');

        $this->assertTrue($method->isVirtual());
        $this->assertEquals(Subject::class, $method->type()->__toString());
        $this->assertCount(2, $method->inferredTypes());
        $types = iterator_to_array($method->inferredTypes());
        $this->assertEquals('string', $types[0]->__toString());
        $this->assertEquals(Subject::class, $types[1]->__toString());
    }
}
