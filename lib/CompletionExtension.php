<?php

namespace Phpactor\Extension\Completion;

use Phpactor\Completion\Core\Formatter\ObjectFormatter;
use Phpactor\Completion\Core\ChainCompletor;
use Phpactor\Completion\Core\TypedCompletor;
use Phpactor\Completion\Core\TypedCompletorRegistry;
use Phpactor\Container\Extension;
use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;
use Phpactor\Container\Container;

class CompletionExtension implements Extension
{
    public const TAG_COMPLETOR = 'completion.completor';
    public const TAG_FORMATTER = 'completion.formatter';
    public const SERVICE_FORMATTER = 'completion.formatter';
    public const SERVICE_REGISTRY = 'completion.registry';
    public const KEY_COMPLETOR_TYPES = 'types';

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $this->registerCompletion($container);
    }

    private function registerCompletion(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_REGISTRY, function (Container $container) {

            foreach ($container->getServiceIdsForTag(self::TAG_COMPLETOR) as $serviceId => $attrs) {
                $completors[] = new TypedCompletor($container->get($serviceId), $attrs[self::KEY_COMPLETOR_TYPES] ?? ['php']);
            }

            return new TypedCompletorRegistry($completors);
        });

        $container->register(self::SERVICE_FORMATTER, function (Container $container) {
            $formatters = [];
            foreach (array_keys($container->getServiceIdsForTag(self::TAG_FORMATTER)) as $serviceId) {
                $taggedFormatters = $container->get($serviceId);
                $taggedFormatters = is_array($taggedFormatters) ? $taggedFormatters : [ $taggedFormatters ];

                foreach ($taggedFormatters as $taggedFormatter) {
                    $formatters[] = $taggedFormatter;
                }
            }

            return new ObjectFormatter($formatters);
        });
    }
}
