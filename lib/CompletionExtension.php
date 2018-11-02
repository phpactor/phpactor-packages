<?php

namespace Phpactor\Extension\Completion;

use Phpactor\Completion\Core\Formatter\ObjectFormatter;
use Phpactor\Completion\Core\ChainCompletor;
use Phpactor\Container\Extension;
use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;
use Phpactor\Container\Container;

class CompletionExtension implements Extension
{
    const TAG_COMPLETOR = 'completion.completor';
    const TAG_FORMATTER = 'completion.formatter';

    const SERVICE_COMPLETOR = 'completion.completor';
    const SERVICE_FORMATTER = 'completion.formatter';

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
        $container->register(self::SERVICE_COMPLETOR, function (Container $container) {
            $completors = [];
            foreach (array_keys($container->getServiceIdsForTag(self::TAG_COMPLETOR)) as $serviceId) {
                $completors[] = $container->get($serviceId);
            }
            return new ChainCompletor($completors);
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
