<?php

namespace Phpactor\Extension\ComposerAutoloader;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\MapResolver\Resolver;
use Composer\Autoload\ClassLoader;
use RuntimeException;
use Webmozart\PathUtil\Path;

class ComposerAutoloaderExtension implements Extension
{
    const SERVICE_AUTOLOADERS = 'composer.class_loaders';

    const PARAM_AUTOLOADER_PATH = 'composer.autoloader_path';
    const PARAM_AUTOLOAD_DEREGISTER = 'composer.autoload_deregister';
    const PARAM_PROJECT_ROOT = 'composer_autoloader.project_root';

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $resolver)
    {
        $resolver->setDefaults([
            self::PARAM_AUTOLOAD_DEREGISTER => true,
            self::PARAM_AUTOLOADER_PATH => 'vendor/autoload.php',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_AUTOLOADERS, function (Container $container) {
            $currentAutoloaders = spl_autoload_functions();
            $autoloaders = [];

            $autoloaderPaths = (array) $container->getParameter(self::PARAM_AUTOLOADER_PATH);

            foreach ($autoloaderPaths as $autoloaderPath) {
                if (false === file_exists($autoloaderPath)) {
                    $this->logAutoloaderNotFound($container, $autoloaderPath);
                    continue;
                }

                $autoloader = require $autoloaderPath;

                if (!$autoloader instanceof ClassLoader) {
                    throw new RuntimeException('Autoloader is not an instance of ClassLoader');
                }

                $autoloaders[] = $autoloader;
            }

            if ($currentAutoloaders && $container->getParameter(self::PARAM_AUTOLOAD_DEREGISTER)) {
                $this->deregisterAutoloader($currentAutoloaders);
            }

            return $autoloaders;
        });
    }

    private function logAutoloaderNotFound(Container $container, $autoloaderPath)
    {
        $container->get(LoggingExtension::SERVICE_LOGGER)->warning(
            sprintf(
                'Could not find autoloader "%s"',
                $autoloaderPath
            )
        );
    }

    private function deregisterAutoloader(array $currentAutoloaders): void
    {
        $autoloaders = spl_autoload_functions();

        if (!$autoloaders) {
            return;
        }

        foreach ($autoloaders as $autoloadFunction) {
            spl_autoload_unregister($autoloadFunction);
        }
        
        foreach ($currentAutoloaders as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }
}
