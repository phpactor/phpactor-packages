<?php

namespace Phpactor\Extension\PhpSpec;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\PhpSpec\MemberProvider\ObjectBehaviorProvider;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\MapResolver\Resolver;

class PhpSpecExtension implements Extension
{
    const PARAM_SPEC_PREFIX = 'phpspec.spec_prefix';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('phpspec.method_provider', function (Container $container) {
            return new ObjectBehaviorProvider($container->getParameter(self::PARAM_SPEC_PREFIX));
        }, [ WorseReflectionExtension::TAG_MEMBER_PROVIDER => []]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_SPEC_PREFIX => 'spec',
        ]);
    }
}
