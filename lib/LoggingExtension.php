<?php

namespace Phpactor\Exension\Logger;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;
use Phpactor\Container\Container;
use Phpactor\Container\Extension;
use Phpactor\MapResolver\Resolver;
use Phpactor\Container\ContainerBuilder;

class LoggingExtension implements Extension
{
    const LOGGING_PATH = 'logging.path';
    const LOGGING_LEVEL = 'logging.level';
    const LOGGING_ENABLED = 'logging.enabled';
    const LOGGING_FINGERS_CROSSED = 'logging.fingers_crossed';

    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::LOGGING_ENABLED => false,
            self::LOGGING_FINGERS_CROSSED => false,
            self::LOGGING_PATH => 'phpactor.log',
            self::LOGGING_LEVEL => LogLevel::WARNING,
        ]);
    }

    public function load(ContainerBuilder $container)
    {
        $container->register('logging.logger', function (Container $container) {
            $logger = new Logger('phpactor');

            if (false === $container->getParameter(self::LOGGING_ENABLED)) {
                $logger->pushHandler(new NullHandler());
                return $logger;
            }

            $handler = new StreamHandler(
                $container->getParameter(self::LOGGING_PATH),
                $container->getParameter(self::LOGGING_LEVEL)
            );

            if ($container->getParameter(self::LOGGING_FINGERS_CROSSED)) {
                $handler = new FingersCrossedHandler($handler);
            }

            $logger->pushHandler($handler);

            return $logger;
        });
    }
}
