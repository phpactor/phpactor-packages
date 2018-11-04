<?php

namespace Phpactor\Extension\Console;

use InvalidArgumentException;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\MapResolver\Resolver;

class ConsoleExtension implements Extension
{
    const TAG_COMMAND = 'console.command';
    const SERVICE_COMMAND_LOADER = 'console.command_loader';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_COMMAND_LOADER, function (Container $container) {
            $map = [];
            foreach ($container->getServiceIdsForTag(self::TAG_COMMAND) as $commandId => $attrs) {
                if (!isset($attrs['name'])) {
                    throw new InvalidArgumentException(sprintf(
                        'Command with service ID "%s" must have the "name" attribute',
                        $commandId
                    ));
                }

                $map[$attrs['name']] = $commandId;
            }

            return new PhpactorCommandLoader($container, $map);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
