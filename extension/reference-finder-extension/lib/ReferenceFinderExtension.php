<?php

namespace Phpactor\Extension\ReferenceFinder;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\ReferenceFinder\ChainDefinitionLocationProvider;
use RuntimeException;

class ReferenceFinderExtension implements Extension
{
    const SERVICE_DEFINITION_LOCATOR = 'reference_finder.definition_locator';
    const SERVICE_IMPLEMENTATION_FINDER = 'reference_finder.implementation_finder';
    const TAG_DEFINITION_LOCATOR = 'reference_finder.definition_locator';
    const TAG_IMPLEMENTATION_FINDER = 'reference_finder.implementation_finder';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_DEFINITION_LOCATOR, function (Container $container) {
            $locators = [];
            foreach (array_keys($container->getServiceIdsForTag(self::TAG_DEFINITION_LOCATOR)) as $serviceId) {
                $locators[] = $container->get($serviceId);
            }

            return new ChainDefinitionLocationProvider($locators, $container->get(LoggingExtension::SERVICE_LOGGER));
        });

        $container->register(self::SERVICE_IMPLEMENTATION_FINDER, function (Container $container) {
            foreach (array_keys($container->getServiceIdsForTag(self::TAG_IMPLEMENTATION_FINDER)) as $serviceId) {
                return $container->get($serviceId);
            }

            throw new RuntimeException(sprintf(
                'At least one implementation must be registered with tag "%s"',
                self::TAG_IMPLEMENTATION_FINDER
            ));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
