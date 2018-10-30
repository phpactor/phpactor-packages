<?php

namespace Phpactor\FilePathResolverExtension;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\FilePathResolver\Expander\ValueExpander;
use Phpactor\FilePathResolver\Expander\Xdg\SuffixExpanderDecorator;
use Phpactor\FilePathResolver\Expander\Xdg\XdgCacheExpander;
use Phpactor\FilePathResolver\Expander\Xdg\XdgConfigExpander;
use Phpactor\FilePathResolver\Expander\Xdg\XdgDataExpander;
use Phpactor\FilePathResolver\Filter\CanonicalizingPathFilter;
use Phpactor\FilePathResolver\Filter\TokenExpandingFilter;
use Phpactor\FilePathResolver\PathResolver;
use Phpactor\MapResolver\Resolver;

class FilePathResolverExtension implements Extension
{
    const SERVICE_FILE_PATH_RESOLVER = 'file_path_resolver.resolver';

    const TAG_FILTER = 'file_path_resolver.filter';
    const TAG_EXPANDER = 'file_path_resolver.expander';

    const PARAM_PROJECT_ROOT = 'file_path_resolver.project_root';
    const PARAM_APP_NAME = 'file_path_resolver.app_name';

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_PROJECT_ROOT => getcwd(),
            self::PARAM_APP_NAME => 'phpactor',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $this->registerPathResolver($container);
        $this->registerFilters($container);
    }

    private function registerPathResolver(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_FILE_PATH_RESOLVER, function (Container $container) {
            $filters = [];
            foreach (array_keys($container->getServiceIdsForTag(self::TAG_FILTER)) as $serviceId) {
                $filters[] = $container->get($serviceId);
            }
        
            return new PathResolver($filters);
        });
    }

    private function registerFilters(ContainerBuilder $container)
    {
        $container->register('file_path_resolver.filter.canonicalizing', function () {
            return new CanonicalizingPathFilter();
        }, [ self::TAG_FILTER => [] ]);

        $container->register('file_path_resolver.filter.token_expanding', function (Container $container) {
            $suffix = DIRECTORY_SEPARATOR . $container->getParameter(self::PARAM_APP_NAME);

            $expanders = [
                new ValueExpander('%project_root%', $container->getParameter(self::PARAM_PROJECT_ROOT)),
                new SuffixExpanderDecorator(new XdgCacheExpander('%cache%'), $suffix),
                new SuffixExpanderDecorator(new XdgConfigExpander('%config%'), $suffix),
                new SuffixExpanderDecorator(new XdgDataExpander('%data%'), $suffix),
            ];

            foreach (array_keys($container->getServiceIdsForTag(self::TAG_EXPANDER)) as $serviceId) {
                $expanders[] = $container->get($serviceId);
            }

            return new TokenExpandingFilter($expanders);
        }, [ self::TAG_FILTER => [] ]);
    }
}
