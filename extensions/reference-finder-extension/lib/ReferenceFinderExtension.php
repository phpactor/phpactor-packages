<?php

namespace Phpactor\Extension\ReferenceFinder;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\ReferenceFinder\ChainDefinitionLocationProvider;

class ReferenceFinderExtension implements Extension
{
    const SERVICE_DEFINITION_LOCATOR = 'reference_finder.definition_locator';
    const TAG_DEFINITION_LOCATOR = 'reference_finder.definition_locator';

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
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
