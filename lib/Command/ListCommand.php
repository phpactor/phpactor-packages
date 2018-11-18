<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Phpactor\Extension\ExtensionManager\Model\ExtensionState;
use Phpactor\Extension\ExtensionManager\Service\ExtensionLister;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * @var ExtensionLister
     */
    private $lister;

    public function __construct(ExtensionLister $lister)
    {
        parent::__construct();
        $this->lister = $lister;
    }

    protected function configure()
    {
        $this->setDescription('List extensions');
        $this->addOption('installed', null, InputOption::VALUE_NONE, 'Only show installed packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            '',
            'Name',
            'Version',
            'Description',
        ]);

        foreach ($this->lister->list($input->getOption('installed'))->sorted() as $extension) {
            $table->addRow([
                $this->formatState($extension->state()),
                $extension->name(),
                $extension->version(),
                $extension->description()
            ]);
        }
        $table->render();
        $output->writeln('<comment>✔: installed, ✔*: fixed (primary) installed package</>');
    }

    private function formatState(ExtensionState $state): string
    {
        if ($state->isPrimary()) {
            return '✔*';
        }
        if ($state->isSecondary()) {
            return '✔';
        }
        return '';
    }
}
