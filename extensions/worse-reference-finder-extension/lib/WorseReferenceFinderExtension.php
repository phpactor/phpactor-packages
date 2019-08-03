<?php

namespace Phpactor\Extension\WorseReferenceFinder;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\WorseReferenceFinder\WorsePlainTextClassDefinitionLocator;
use Phpactor\WorseReferenceFinder\WorseReflectionDefinitionLocator;

class WorseReferenceFinderExtension implements Extension
{
    const PARAM_BREAK_CHARS = 'worse_reference_finder.plain_text_break_chars';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('worse_reference_finder.definition_locator.reflection', function (Container $container) {
            return new WorseReflectionDefinitionLocator(
                $container->get(WorseReflectionExtension::SERVICE_REFLECTOR)
            );
        }, [ ReferenceFinderExtension::TAG_DEFINITION_LOCATOR => []]);

        $container->register('worse_reference_finder.definition_locator.plain_text_class', function (Container $container) {
            return new WorsePlainTextClassDefinitionLocator(
                $container->get(WorseReflectionExtension::SERVICE_REFLECTOR),
                $container->getParameter(self::PARAM_BREAK_CHARS)
            );
        }, [ ReferenceFinderExtension::TAG_DEFINITION_LOCATOR => []]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_BREAK_CHARS => [' ', '"', '\'', '|', '%', '(', ')', '[', ']',':',"\r\n", "\n", "\r"]
        ]);
        $schema->setTypes([
            self::PARAM_BREAK_CHARS => 'array',
        ]);
    }
}
