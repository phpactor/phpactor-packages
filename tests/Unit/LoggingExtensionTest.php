<?php

namespace Phpactor\Extension\Logger\Tests\Unit;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\Container;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Logger\LoggingExtension;

class LoggingExtensionTest extends TestCase
{
    public function testLoggingDisabled()
    {
        $container = $this->create([
            LoggingExtension::PARAM_ENABLED => false,
        ]);
        $logger = $container->get('logging.logger');
        assert($logger instanceof Logger);
        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(NullHandler::class, $handlers[0]);
    }

    public function testLoggingEnabled()
    {
        $container = $this->create([
            LoggingExtension::PARAM_ENABLED => true,
        ]);
        $logger = $container->get('logging.logger');
        assert($logger instanceof Logger);
        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(StreamHandler::class, $handlers[0]);
    }

    public function testFingersCrossed()
    {
        $container = $this->create([
            LoggingExtension::PARAM_ENABLED => true,
            LoggingExtension::PARAM_FINGERS_CROSSED => true,
        ]);
        $logger = $container->get('logging.logger');
        assert($logger instanceof Logger);
        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(FingersCrossedHandler::class, $handlers[0]);
    }

    private function create(array $options): Container
    {
        $container = PhpactorContainer::fromExtensions([
            LoggingExtension::class
        ], $options);

        return $container;
    }
}
