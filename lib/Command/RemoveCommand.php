<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Composer\Composer;
use Composer\Installer;
use Phpactor\Container\Container;
use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Command
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setDescription('Remove extensions');
        $this->addArgument('extension', InputArgument::OPTIONAL|InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->removeExtensions($input, $output);

        $installer = $this->container->get('extension_manager.installer');

        if (count($input->getArgument('extension'))) {
            $installer->setUpdate(true);
        }

        $installer->run();
    }

    private function removeExtensions(InputInterface $input, OutputInterface $output)
    {
        $addExtension = $this->container->get('extension_manager.model.add_extension');
        
        foreach ($input->getArgument('extension') as $extension) {
            $addExtension->remove($extension);
        }
    }
}
