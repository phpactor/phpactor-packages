<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Phpactor\Extension\ExtensionManager\Model\AddExtension;
use Phpactor\Extension\ExtensionManager\Service\InstallerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var AddExtension
     */
    private $addExtension;

    public function __construct(InstallerService $installer)
    {
        parent::__construct();
        $this->installer = $installer;
    }

    protected function configure()
    {
        $this->setDescription('Install extensions');
        $this->addArgument('extension', InputArgument::OPTIONAL|InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->requireExtensions($input, $output);

        if (count($input->getArgument('extension'))) {
            $this->installer->installForceUpdate();
            return 0;
        }

        $this->installer->install();
    }

    private function requireExtensions(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getArgument('extension') as $extension) {
            $version = $this->installer->addExtension($extension);
            $output->writeln(sprintf('Using version <info>%s</> of %s', $version, $extension));
        }
    }
}
