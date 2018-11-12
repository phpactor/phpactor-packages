<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Composer\Composer;
use Composer\Installer;
use Phpactor\Container\Container;
use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Model\RemoveExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveCommand extends Command
{
    const ARG_EXTENSION_NAME = 'extension';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RemoveExtension
     */
    private $removeExtension;


    public function __construct(Container $container, RemoveExtension $removeExtension)
    {
        parent::__construct();
        $this->container = $container;
        $this->removeExtension = $removeExtension;
    }

    protected function configure()
    {
        $this->setDescription('Remove extensions');
        $this->addArgument(self::ARG_EXTENSION_NAME, InputArgument::OPTIONAL|InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $extensionNames = $this->resolveExtensionNamesToRemove($input, $style);

        if (null === $extensionNames) {
            return 0;
        }

        $this->removeExtensions($extensionNames, $output);
        $this->runInstall($input);
    }

    private function removeExtensions(array $extensions, OutputInterface $output)
    {
        foreach ($extensions as $extension) {
            $output->writeln(sprintf('<info>Removing:</> %s', $extension));
            $this->removeExtension->remove($extension);
        }
    }

    private function runInstall(InputInterface $input)
    {
        $installer = $this->container->get('extension_manager.installer');
        
        if (count($input->getArgument(self::ARG_EXTENSION_NAME))) {
            $installer->setUpdate(true);
        }
        
        $installer->run();
    }

    private function resolveExtensionNamesToRemove(InputInterface $input, SymfonyStyle $style): ?array
    {
        $extensionNames = $input->getArgument(self::ARG_EXTENSION_NAME);
        $dependents = $this->removeExtension->findDependentExtensions($extensionNames);
        
        if ($dependents) {
            $style->text(sprintf('Package(s) "<info>%s</>" depends on the following packages:', implode('</>", "<info>', $extensionNames)));
            $style->listing($dependents);
            $response = $style->confirm('Remove all of the above packages?', false);
        
            if (false === $response) {
                return null;
            }
        }
        
        return array_merge($extensionNames, $dependents);
    }
}
