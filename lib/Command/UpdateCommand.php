<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Composer\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    /**
     * @var Installer
     */
    private $installer;

    public function __construct(Installer $installer)
    {
        parent::__construct();
        $this->installer = $installer;
    }

    protected function configure()
    {
        $this->setDescription('Update extensions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->installer->setUpdate(true);
        $this->installer->run();
    }
}
