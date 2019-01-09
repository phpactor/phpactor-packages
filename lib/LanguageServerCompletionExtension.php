<?php

namespace Phpactor\Extension\LanguageServerCompletion;

use Phpactor\Completion\Core\ChainSignatureHelper;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\LanguageServerCompletion\Handler\SignatureHelpHandler;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\LanguageServerCompletion\Handler\CompletionHandler;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\MapResolver\Resolver;

class LanguageServerCompletionExtension implements Extension
{
    const PARAM_PROVIDE_TEXT_EDIT = 'language_server_completion.provide_text_edit';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('worse_language_server.handler.completion', function (Container $container) {
            return new CompletionHandler(
                $container->get(LanguageServerExtension::SERVICE_SESSION_WORKSPACE),
                $container->get(CompletionExtension::SERVICE_REGISTRY)
            );
        }, [ LanguageServerExtension::TAG_SESSION_HANDLER => [
            'methods' => [
                'textDocument/completion'
            ]
        ]]);

        $container->register('worse_language_server.handler.signature_help', function (Container $container) {
            $helpers = [];

            $helper = null;
            foreach (array_keys($container->getServiceIdsForTag('language_server_completion.handler.signature_help')) as $serviceId) {
                $helpers[] = $container->get($serviceId);
            }

            $chainHelper = new ChainSignatureHelper(
                $container->get(LoggingExtension::SERVICE_LOGGER),
                $helpers
            );

            return new SignatureHelpHandler(
                $container->get(LanguageServerExtension::SERVICE_SESSION_WORKSPACE),
                $chainHelper
            );
        }, [ LanguageServerExtension::TAG_SESSION_HANDLER => [
            'methods' => [
                'textDocument/completion'
            ]
        ]]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_PROVIDE_TEXT_EDIT => false,
        ]);
    }
}
