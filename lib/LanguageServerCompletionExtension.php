<?php

namespace Phpactor\Extension\LanguageServerCompletion;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\LanguageServerCompletion\Handler\CompletionHandler;
use Phpactor\MapResolver\Resolver;

class LanguageServerCompletionExtension implements Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('worse_language_server.handler.completion', function (Container $container) {
            return new CompletionHandler(
                $container->get(LanguageServerExtension::SERVICE_SESSION_MANAGER),
                $container->get(CompletionExtension::SERVICE_REGISTRY)
            );
        }, [ LanguageServerExtension::TAG_HANDLER => []]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
