<?php

namespace Phpactor\Extension\PhpSpec\MemberProvider;

use PhpSpec\Wrapper\Subject;
use Phpactor\WorseReflection\Core\ClassName;
use Phpactor\WorseReflection\Core\Exception\NotFound;
use Phpactor\WorseReflection\Core\Reflection\Collection\ReflectionMemberCollection;
use Phpactor\WorseReflection\Core\Reflection\ReflectionClassLike;
use Phpactor\WorseReflection\Core\ServiceLocator;
use Phpactor\WorseReflection\Core\Type;
use Phpactor\WorseReflection\Core\Types;
use Phpactor\WorseReflection\Core\Virtual\Collection\VirtualReflectionMemberCollection;
use Phpactor\WorseReflection\Core\Virtual\Collection\VirtualReflectionMethodCollection;
use Phpactor\WorseReflection\Core\Virtual\ReflectionMemberProvider;
use Phpactor\WorseReflection\Core\Virtual\VirtualReflectionMethod;

class ObjectBehaviorProvider implements ReflectionMemberProvider
{
    const CLASS_OBJECT_BEHAVIOR = 'PhpSpec\\ObjectBehavior';
    const SPEC_SUFFIX = 'Spec';

    /**
     * @var string
     */
    private $specPrefix;

    public function __construct(string $specPrefix = 'spec')
    {
        $this->specPrefix = $specPrefix;
    }

    public function provideMembers(
        ServiceLocator $serviceLocator,
        ReflectionClassLike $class
    ): ReflectionMemberCollection {
        $subjectClassName = explode('\\', $class->name()->namespace());

        if (false === $this->isSpecCandidate($class, $subjectClassName)) {
            return VirtualReflectionMemberCollection::fromMembers([]);
        }

        array_shift($subjectClassName);
        $subjectClassName[] = substr($class->name()->short(), 0, -4);
        $subjectClassName = implode('\\', $subjectClassName);

        try {
            $subjectClass = $serviceLocator->reflector()->reflectClass($subjectClassName);
        } catch (NotFound $e) {
            $serviceLocator->logger()->warning(sprintf(
                'Phpspec extension could not locate inferred class name "%s" '.
                'for spec class "%s": %s',
                $class->name()->full(),
                $subjectClassName,
                $e->getMessage()
            ));
            return VirtualReflectionMethodCollection::fromReflectionMethods([]);
        }

        $virtualMethods = [];
        foreach ($subjectClass->methods() as $subjectMethod) {
            $method = VirtualReflectionMethod::fromReflectionMethod($subjectMethod);
            $method = $method->withInferredTypes(
                $method->inferredTypes()->merge(
                    Types::fromTypes([
                        Type::fromString(Subject::class)
                    ])
                )
            );
            $method = $method->withType(Type::fromString(Subject::class));
            $virtualMethods[] = $method;
        }

        return VirtualReflectionMemberCollection::fromMembers($virtualMethods);
    }

    private function isSpecCandidate(ReflectionClassLike $class, array $subjectClassName): bool
    {
        if (!$class->isInstanceOf(ClassName::fromString(self::CLASS_OBJECT_BEHAVIOR))) {
            return false;
        }
        
        if (array_shift($subjectClassName) !== $this->specPrefix) {
            return false;
        }
        
        $suffix = substr($class->name()->short(), -4);
        if ($suffix !== self::SPEC_SUFFIX) {
            return false;
        }

        return true;
    }
}
