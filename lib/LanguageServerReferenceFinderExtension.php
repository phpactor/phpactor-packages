<?php

namespace Phpactor\Extension\LanguageServerReferenceFinder;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\LanguageServerReferenceFinder\Handler\GotoDefinitionHandler;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\MapResolver\Resolver;

class LanguageServerReferenceFinderExtension implements Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('worse_language_server.handler.goto_definition', function (Container $container) {
            return new GotoDefinitionHandler(
                $container->get(LanguageServerExtension::SERVICE_SESSION_MANAGER),
                $container->get(ReferenceFinderExtension::SERVICE_DEFINITION_LOCATOR)
            );
        }, [ LanguageServerExtension::TAG_HANDLER => [
            'metods' => [
                'textDocument/definition'
            ]
        ]]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
