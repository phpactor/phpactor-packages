<?php

namespace Phpactor\Extension\ExtensionManager;

use Composer\Composer;
use Composer\DependencyResolver\Pool;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\Version\VersionSelector;
use Composer\Repository\CompositeRepository;
use Composer\Repository\FilesystemRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\ExtensionManager\Adapter\Composer\ComposerAddExtension;
use Phpactor\Extension\ExtensionManager\Adapter\Composer\ComposerDepdendentPackageFinder;
use Phpactor\Extension\ExtensionManager\Adapter\Composer\ComposerRemoveExtension;
use Phpactor\Extension\ExtensionManager\Adapter\Composer\LazyComposerInstaller;
use Phpactor\Extension\ExtensionManager\Command\InstallCommand;
use Phpactor\Extension\ExtensionManager\Command\ListCommand;
use Phpactor\Extension\ExtensionManager\Command\RemoveCommand;
use Phpactor\Extension\ExtensionManager\Command\UpdateCommand;
use Phpactor\Extension\ExtensionManager\EventSubscriber\PostInstallSubscriber;
use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionFileGenerator;
use Phpactor\Extension\ExtensionManager\Model\RemoveExtension;
use Phpactor\Extension\ExtensionManager\Service\ExtensionLister;
use Phpactor\Extension\ExtensionManager\Service\InstallerService;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\MapResolver\Resolver;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Webmozart\PathUtil\Path;

class ExtensionManagerExtension implements Extension
{
    const PARAM_EXTENSION_REPOSITORY_FILE = 'extension_manager.extension_dirname';
    const PARAM_INSTALLED_EXTENSIONS_FILE = 'extension_manager.extension_list_path';
    const PARAM_EXTENSION_VENDOR_DIR = 'extension_manager.extension_vendor_dir';
    const PARAM_ROOT_PACKAGE_NAME = 'extension_manager.root_package_name';
    const PARAM_EXTENSION_CONFIG_FILE = 'extension_manager.config_path';
    const PARAM_VENDOR_DIR = 'extension_manager.vendor_dir';

    public function configure(Resolver $resolver): void
    {
        $resolver->setRequired([
            self::PARAM_EXTENSION_VENDOR_DIR,
            self::PARAM_VENDOR_DIR,
            self::PARAM_EXTENSION_CONFIG_FILE,
            self::PARAM_INSTALLED_EXTENSIONS_FILE,
        ]);

        $resolver->setDefaults([
            self::PARAM_ROOT_PACKAGE_NAME => 'phpactor-extensions',
        ]);
    }

    public function load(ContainerBuilder $container): void
    {
        $this->registerCommands($container);
        $this->registerComposer($container);
        $this->registerModel($container);
        $this->registerService($container);
    }

    private function registerCommands(ContainerBuilder $container)
    {
        $container->register('extension_manager.command.install-extension', function (Container $container) {
            return new InstallCommand(
                $container->get('extension_manager.service.installer')
            );
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:install' ] ]);

        $container->register('extension_manager.command.list', function (Container $container) {
            return new ListCommand($container->get('extension_manager.service.lister'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:list' ] ]);

        $container->register('extension_manager.command.update', function (Container $container) {
            return new UpdateCommand($container->get('extension_manager.installer'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:update' ] ]);

        $container->register('extension_manager.command.update', function (Container $container) {
            return new RemoveCommand(
                $container->get('extension_manager.model.installer'),
                $container->get('extension_manager.model.dependency_finder'),
                $container->get('extension_manager.model.remove_extension')
            );
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:remove' ] ]);
    }

    private function registerComposer(ContainerBuilder $container)
    {
        $container->register('extension_manager.composer', function (Container $container) {
            return $this->createComposer($container);
        });

        $container->register('extension_manager.composer_for_installer', function (Container $container) {
            // after modifying the config file, it is necessary to re-initialize composer in order
            // that it takes notice of the modifications. instead of doing this we simply provide the
            // installer with a new instance of composer (rather than the original instance which we need
            // to instantiate earlier).
            return $this->createComposer($container);
        });
        
        $container->register('extension_manager.installer', function (Container $container) {
            $composer = $container->get('extension_manager.composer');
            $installer = Installer::create(
                $container->get('extension_manager.io'),
                $container->get('extension_manager.composer_for_installer')
            );
            $installer->setAdditionalInstalledRepository($container->get('extension_manager.repository.local'));

            return $installer;
        });
        
        $container->register('extension_manager.io', function (Container $container) {
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

        $container->register('extension_manager.repository.local', function (Container $container) {
            return new InstalledFilesystemRepository(new JsonFile($this->repositoryFile($container)));
        });

        $container->register('extension_manager.repository.combined', function (Container $container) {
            return new CompositeRepository([
                $container->get('extension_manager.repository.local'),
                new InstalledFilesystemRepository(new JsonFile($this->extensionRepositoryFile($container)))
            ]);
        });

        $container->register('extension_manager.repository.pool', function (Container $container) {
            $pool = new Pool('dev');

            $repositoryManager = $container->get('extension_manager.composer')->getRepositoryManager();

            foreach ($repositoryManager->getRepositories() as $repository) {
                $pool->addRepository($repository);
            }

            return $pool;
        });

        $container->register('extension_manager.version_selector', function (Container $container) {
            return new VersionSelector($container->get('extension_manager.repository.pool'));
        });
    }

    private function initialize(Container $container): void
    {
        $path = $container->getParameter(self::PARAM_EXTENSION_CONFIG_FILE);
        
        if (file_exists($path)) {
            return;
        }

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, json_encode([
            'config' => [
                'name' => $container->getParameter(self::PARAM_ROOT_PACKAGE_NAME),
                'vendor-dir' => $container->getParameter(self::PARAM_EXTENSION_VENDOR_DIR),
            ]
        ], JSON_PRETTY_PRINT));
    }

    private function repositoryFile(Container $container)
    {
        return Path::join([
            $container->getParameter(self::PARAM_VENDOR_DIR),
            'composer',
            'installed.json'
        ]);
    }

    private function extensionRepositoryFile(Container $container)
    {
        return Path::join([
            $container->getParameter(self::PARAM_EXTENSION_VENDOR_DIR),
            'composer',
            'installed.json'
        ]);
    }

    private function registerModel(ContainerBuilder $container)
    {
        $container->register('extension_manager.model.add_extension', function (Container $container) {
            return new ComposerAddExtension(
                $container->get('extension_manager.repository.combined'),
                $container->getParameter(self::PARAM_EXTENSION_CONFIG_FILE),
                $container->get('extension_manager.version_selector')
            );
        });
        $container->register('extension_manager.model.remove_extension', function (Container $container) {
            return new ComposerRemoveExtension(
                $container->get('extension_manager.repository.combined'),
                $container->getParameter(self::PARAM_EXTENSION_CONFIG_FILE)
            );
        });
        $container->register('extension_manager.model.installer', function (Container $container) {
            return new LazyComposerInstaller($container);
        });
        $container->register('extension_manager.model.dependency_finder', function (Container $container) {
            return new ComposerDepdendentPackageFinder($container->get('extension_manager.repository.combined'));
        });
    }

    private function createComposer(Container $container): Composer
    {
        $this->initialize($container);
        
        $composer = Factory::create(
            $container->get('extension_manager.io'),
            $container->getParameter(self::PARAM_EXTENSION_CONFIG_FILE)
        );
        
        $composer->getEventDispatcher()->addSubscriber(new PostInstallSubscriber(
            new ExtensionFileGenerator($container->getParameter(self::PARAM_INSTALLED_EXTENSIONS_FILE))
        ));
        return $composer;
    }

    private function registerService(ContainerBuilder $container)
    {
        $container->register('extension_manager.service.installer', function (Container $container) {
            return new InstallerService(
                $container->get('extension_manager.model.installer'),
                $container->get('extension_manager.model.add_extension')
            );
        });

        $container->register('extension_manager.service.lister', function (Container $container) {
            return new ExtensionLister(
                $container->get('extension_manager.repository.combined')
            );
        });
    }
}
