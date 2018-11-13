<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Composer\Composer;
use Composer\Installer;
use Composer\Repository\RepositoryInterface;
use Phpactor\Composer\PhpactorExtensionPackage;
use Phpactor\Extension\ExtensionManager\Service\ExtensionLister;
use Phpactor\Extension\ExtensionManager\Util\PackageFilter;
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
                $extension->name(),
                $extension->version(),
                $extension->description()
            ]);
        }
        $table->render();
    }
}
