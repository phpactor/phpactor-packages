<?php

namespace Phpactor\Extension\ClassToFile;

use Phpactor\Container\Extension;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\ClassFileConverter\Domain\ClassToFileFileToClass;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Phpactor\ClassFileConverter\Adapter\Simple\SimpleClassToFile;
use Phpactor\ClassFileConverter\Domain\ChainClassToFile;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerFileToClass;
use Phpactor\ClassFileConverter\Adapter\Simple\SimpleFileToClass;
use Phpactor\Container\Container;
use Phpactor\ClassFileConverter\Domain\ChainFileToClass;

class ClassToFileExtension implements Extension
{
    const SERVICE_CONVERTER = 'class_to_file.converter';
    const PARAM_PROJECT_ROOT = 'class_to_file.project_root';

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PARAM_PROJECT_ROOT => getcwd(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register(self::SERVICE_CONVERTER, function (Container $container) {
            return new ClassToFileFileToClass(
                $container->get('class_to_file.class_to_file'),
                $container->get('class_to_file.file_to_class')
            );
        });

        $container->register('class_to_file.class_to_file', function (Container $container) {
            $classToFiles = [];
            foreach ($container->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS) as $classLoader) {
                $classToFiles[] = new ComposerClassToFile($classLoader);
            }

            if (empty($classToFiles)) {
                $projectDir = $container->getParameter(self::PARAM_PROJECT_ROOT);
                $classToFiles[] = new SimpleClassToFile($projectDir);
            }

            return new ChainClassToFile($classToFiles);
        });

        $container->register('class_to_file.file_to_class', function (Container $container) {
            $fileToClasses = [];
            foreach ($container->get(ComposerAutoloaderExtension::SERVICE_AUTOLOADERS) as $classLoader) {
                $fileToClasses[] =  new ComposerFileToClass($classLoader);
            }

            if (empty($fileToClasses)) {
                $fileToClasses[] = new SimpleFileToClass();
            }

            return new ChainFileToClass($fileToClasses);
        });
    }
}
