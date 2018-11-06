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
    const PARAM_EXTENSION_PATH = 'extension_manager.extension_dirname';
    const PARAM_EXTENSION_LIST_PATH = 'extension_manager.extension_list_path';
    const PARAM_EXTENSION_VENDOR_DIR = 'extension_manager.extension_vendor_dir';
    const PARAM_ROOT_PACKAGE_NAME = 'extension_manager.root_package_name';
    const PARAM_EXTENSION_CONFIG_PATH = 'extension_manager.config_path';

    public function configure(Resolver $resolver): void
    {
        $resolver->setDefaults([
            self::PARAM_EXTENSION_CONFIG_PATH => '%project_root%/extensions.json',
            self::PARAM_EXTENSION_PATH => '%project_root%/vendor-ext/composer/installed.json',
            self::PARAM_EXTENSION_VENDOR_DIR => '%project_root%/vendor-ext',
            self::PARAM_EXTENSION_LIST_PATH => '%cache%/phpactor-extensions.php',
            self::PARAM_ROOT_PACKAGE_NAME => 'extension-manager',
        ]);
    }

    public function load(ContainerBuilder $container): void
    {
        $this->registerCommands($container);
        $this->registerComposer($container);
    }

    private function registerCommands(ContainerBuilder $container)
    {
        $container->register('extension_manager.command.install-extension', function (Container $container) {
            return new InstallCommand($container->get('extension_manager.installer'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:install' ] ]);

        $container->register('extension_manager.command.list', function (Container $container) {
            return new ListCommand($container->get('extension_manager.repository.combined'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'extension:list' ] ]);
    }

    private function registerComposer(ContainerBuilder $container)
    {
        $container->register('extension_manager.composer', function (Container $container) {
            $this->initialize($container);

            $composer = Factory::create(
                $container->get('extension_manager.io'),
                $this->resolvePath($container, self::PARAM_EXTENSION_CONFIG_PATH)
            );
            $composer->getEventDispatcher()->addSubscriber(new PostInstallSubscriber(
                new ExtensionWriter($this->resolvePath($container, self::PARAM_EXTENSION_LIST_PATH))
            ));

            return $composer;
        });
        
        $container->register('extension_manager.installer', function (Container $container) {
            $composer = $container->get('extension_manager.composer');
            $installer = Installer::create(
                $container->get('extension_manager.io'),
                $container->get('extension_manager.composer')
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
            return new InstalledFilesystemRepository(new JsonFile(
                __DIR__ . '/../vendor/composer/installed.json'
            ));
        });

        $container->register('extension_manager.repository.combined', function (Container $container) {
            return new CompositeRepository([
                $container->get('extension_manager.repository.local'),
                new InstalledFilesystemRepository(new JsonFile(
                    $this->resolvePath($container, self::PARAM_EXTENSION_PATH)
                ))
            ]);
        });
    }

    private function initialize(Container $container): void
    {
        $path = $this->resolvePath($container, self::PARAM_EXTENSION_CONFIG_PATH);
        
        if (file_exists($path)) {
            return;
        }

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, json_encode([
            'config' => [
                'name' => $container->getParameter(self::PARAM_ROOT_PACKAGE_NAME),
                'vendor-dir' => $this->resolvePath($container, self::PARAM_EXTENSION_PATH),
            ]
        ], JSON_PRETTY_PRINT));
    }

    private function resolvePath(Container $container, string $param)
    {
        $resolver = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER);

        return $resolver->resolve($container->getParameter($param));
    }
}
