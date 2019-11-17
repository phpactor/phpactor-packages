<?php

namespace Phpactor\Extension\WorseReferenceFinder;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\Extension\SourceCodeFilesystem\SourceCodeFilesystemExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\WorseReferenceFinder\SourceCodeFilesystem\SimilarityPreFilter\AbstractnessFilter;
use Phpactor\WorseReferenceFinder\SourceCodeFilesystem\SimilarityPreFilter\ChainFilter;
use Phpactor\WorseReferenceFinder\SourceCodeFilesystem\SimilarityPreFilter\PathFqnSimilarityFilter;
use Phpactor\WorseReferenceFinder\SourceCodeFilesystem\WorseClassImplementationFinder;
use Phpactor\WorseReferenceFinder\WorsePlainTextClassDefinitionLocator;
use Phpactor\WorseReferenceFinder\WorseReflectionDefinitionLocator;

class WorseReferenceFinderExtension implements Extension
{
    const PARAM_BREAK_CHARS = 'worse_reference_finder.plain_text_break_chars';
    const PARAM_ENABLE_SIMILARITY_FILTER = 'worse_reference_finder.implementation_finder.similarity_filter';
    const PARAM_ENABLE_ABSTRACTNESS_FILTER = 'worse_reference_finder.implementation_finder.abstractness_filter';

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

        $container->register('worse_reference_finder.implementation_finder.worse', function (Container $container) {
            $filters = [];

            if ($container->getParameter(self::PARAM_ENABLE_ABSTRACTNESS_FILTER)) {
                $filters[] = new AbstractnessFilter();
            }

            if ($container->getParameter(self::PARAM_ENABLE_SIMILARITY_FILTER)) {
                $filters[] = new PathFqnSimilarityFilter();
            }

            return new WorseClassImplementationFinder(
                $container->get(WorseReflectionExtension::SERVICE_REFLECTOR),
                $container->get(SourceCodeFilesystemExtension::SERVICE_FILESYSTEM_COMPOSER),
                new ChainFilter(...$filters)
            );
        }, [ ReferenceFinderExtension::TAG_IMPLEMENTATION_FINDER=> []]);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_ENABLE_ABSTRACTNESS_FILTER => true,
            self::PARAM_ENABLE_SIMILARITY_FILTER => true,
            self::PARAM_BREAK_CHARS => [' ', '"', '\'', '|', '%', '(', ')', '[', ']',':',"\r\n", "\n", "\r"]
        ]);
        $schema->setTypes([
            self::PARAM_BREAK_CHARS => 'array',
        ]);
    }
}
