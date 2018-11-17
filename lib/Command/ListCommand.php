<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Service\ExtensionLister;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'Name',
            'Version',
            'Description',
        ]);

        foreach ($this->lister->list() as $extension) {
            $table->addRow([
                $this->formatName($extension),
                $extension->version(),
                $extension->description()
            ]);
        }
        $table->render();

        $output->writeln('(*) fixed packages');
    }

    private function formatName(Extension $extension)
    {
        $name = $extension->name();

        if ($extension->isPrimary()) {
            return $name . '<comment>*</>';
        }

        return $name;
    }
}
