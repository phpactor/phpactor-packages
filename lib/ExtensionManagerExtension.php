<?php

namespace Phpactor\Extension\ExtensionManager;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Repository\CompositeRepository;
use Composer\Repository\FilesystemRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\ExtensionManager\Command\InstallCommand;
use Phpactor\Extension\ExtensionManager\Command\ListCommand;
use Phpactor\Extension\ExtensionManager\EventSubscriber\PostInstallSubscriber;
use Phpactor\Extension\ExtensionManager\Model\ExtensionWriter;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\MapResolver\Resolver;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

class ExtensionManagerExtension implements Extension
{
    const PARAM_EXTENSION_FILENAME = 'composer.extension.filename';
    const PARAM_EXTENSION_PATH = 'composer.extension_dirname';
    const PARAM_EXTENSION_LIST_PATH = 'composer.extension_list_path';

    public function configure(Resolver $resolver): void
    {
        $resolver->setDefaults([
            self::PARAM_EXTENSION_PATH => '%data%/extensions/composer/installed.json',
            self::PARAM_EXTENSION_LIST_PATH => '%cache%/phpactor-extensions.php',
        ]);
    }

    public function load(ContainerBuilder $container): void
    {
        $this->registerCommands($container);
        $this->registerComposer($container);
    }

    private function registerCommands(ContainerBuilder $container)
    {
        $container->register('composer.command.install-extension', function (Container $container) {
            return new InstallCommand($container->get('composer.installer'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:install' ] ]);

        $container->register('composer.command.list', function (Container $container) {
            return new ListCommand($container->get('composer.repository.combined'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:list' ] ]);
    }

    private function registerComposer(ContainerBuilder $container)
    {
        $container->register('composer.composer', function (Container $container) {
            $this->initialize($container);

            $composer = Factory::create(
                $container->get('composer.io'),
                $this->resolvePath($container, self::PARAM_EXTENSION_PATH)
            );
            $composer->getEventDispatcher()->addSubscriber(new PostInstallSubscriber(
                new ExtensionWriter($this->resolvePath($container, self::PARAM_EXTENSION_LIST_PATH))
            ));

            return $composer;
        });
        
        $container->register('composer.installer', function (Container $container) {
            $composer = $container->get('composer.composer');
            $installer = Installer::create(
                $container->get('composer.io'),
                $container->get('composer.composer')
            );
            $installer->setAdditionalInstalledRepository($container->get('composer.repository.local'));

            return $installer;
        });
        
        $container->register('composer.io', function (Container $container) {
            $helperSet  = new HelperSet([
                'question' => new QuestionHelper(),
            ]);
            return new ConsoleIO(
                $container->get('extension_manager.console.input'),
                $container->get('extension_manager.console.output'),
                $helperSet
            );
            
        });

        $container->register('extension_manager.console.input', function () {
            return new ArgvInput();
        });

        $container->register('extension_manager.console.output', function () {
            return new ConsoleOutput();
        });


        $container->register('composer.repository.local', function (Container $container) {
            return new InstalledFilesystemRepository(new JsonFile(
                __DIR__ . '/../vendor/composer/installed.json'
            ));
        });

        $container->register('composer.repository.combined', function (Container $container) {
            return new CompositeRepository([
                $container->get('composer.repository.local'),
                new InstalledFilesystemRepository(new JsonFile(
                    $this->resolvePath($container, self::PARAM_EXTENSION_PATH)
                ))
            ]);
        });
    }

    public function initialize(Container $container): void
    {
        $path = $this->resolvePath($container, self::PARAM_EXTENSION_PATH);
        
        if (file_exists($path)) {
            return;
        }

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, json_encode([
            'config' => [
                'name' => 'phpactor',
                'vendor-dir' => $container->getParameter(self::PARAM_EXTENSION_PATH)
            ]
        ], JSON_PRETTY_PRINT));
    }

    private function resolvePath(Container $container, string $param)
    {
        $resolver = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER);

        return $resolver->resolve($container->getParameter($param));
    }
}
