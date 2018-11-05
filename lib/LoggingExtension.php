<?php

namespace Phpactor\Extension\Logger;

use Composer\Autoload\ClassLoader;
use Monolog\Handler\NullHandler;
use Phpactor\Extension\Core\Application\Helper\ClassFileNormalizer;
use Phpactor\Filesystem\Domain\Cwd;
use Phpactor\Extension\Core\Console\Dumper\DumperRegistry;
use Phpactor\Extension\Core\Console\Dumper\IndentedDumper;
use Phpactor\Extension\Core\Console\Dumper\JsonDumper;
use Phpactor\Extension\Core\Console\Dumper\TableDumper;
use Phpactor\Extension\Core\Console\Prompt\BashPrompt;
use Phpactor\Extension\Core\Console\Prompt\ChainPrompt;
use Symfony\Component\Console\Application;
use Phpactor\Extension\Core\Command\ConfigDumpCommand;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;
use Phpactor\Extension\Core\Application\CacheClear;
use Phpactor\Extension\Core\Command\CacheClearCommand;
use Phpactor\Extension\Core\Application\Status;
use Phpactor\Extension\Core\Command\StatusCommand;
use Phpactor\Container\Container;
use Phpactor\Container\Extension;
use Phpactor\MapResolver\Resolver;
use Phpactor\Container\ContainerBuilder;

class LoggingExtension implements Extension
{
    const SERVICE_LOGGER = 'logging.logger';

    const PARAM_PATH = 'logging.path';
    const PARAM_LEVEL = 'logging.level';
    const PARAM_ENABLED = 'logging.enabled';
    const PARAM_FINGERS_CROSSED = 'logging.fingers_crossed';

    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_ENABLED => false,
            self::PARAM_FINGERS_CROSSED => false,
            self::PARAM_PATH => 'phpactor.log',
            self::PARAM_LEVEL => LogLevel::WARNING,
        ]);
    }

    public function load(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_LOGGER, function (Container $container) {
            $logger = new Logger('phpactor');

            if (false === $container->getParameter(self::PARAM_ENABLED)) {
                $logger->pushHandler(new NullHandler());
                return $logger;
            }

            $handler = new StreamHandler(
                $container->getParameter(self::PARAM_PATH),
                $container->getParameter(self::PARAM_LEVEL)
            );

            if ($container->getParameter(self::PARAM_FINGERS_CROSSED)) {
                $handler = new FingersCrossedHandler($handler);
            }

            $logger->pushHandler($handler);

            return $logger;
        });
    }
}
