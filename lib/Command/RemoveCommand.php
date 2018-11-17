<?php

namespace Phpactor\Extension\ExtensionManager\Command;

use Phpactor\Extension\ExtensionManager\Service\RemoverService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveCommand extends Command
{
    const ARG_EXTENSION_NAME = 'extension';

    /**
     * @var RemoverService
     */
    private $remover;

    public function __construct(RemoverService $remover)
    {
        parent::__construct();
        $this->remover = $remover;
    }

    protected function configure()
    {
        $this->setDescription('Remove extensions');
        $this->addArgument(self::ARG_EXTENSION_NAME, InputArgument::REQUIRED|InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionNames = $this->resolveExtensionNamesToRemove(
            $input,
            new SymfonyStyle($input, $output)
        );

        if (null === $extensionNames) {
            return 0;
        }

        $this->removeExtensions($extensionNames, $output);
        $this->remover->installForceUpdate();
    }

    private function removeExtensions(array $extensions, OutputInterface $output)
    {
        foreach ($extensions as $extension) {
            $output->writeln(sprintf('<info>Removing:</> %s', $extension));
            $this->remover->removeExtension($extension);
        }
    }

    private function resolveExtensionNamesToRemove(InputInterface $input, SymfonyStyle $style): ?array
    {
        $extensionNames = (array) $input->getArgument(self::ARG_EXTENSION_NAME);
        $dependents = $this->remover->findDependentExtensions($extensionNames);
        
        if ($dependents) {
            $style->text(sprintf('Package(s) "<info>%s</>" are dependencies of the following packages:', implode('</>", "<info>', $extensionNames)));
            $style->listing($dependents);

            if ($input->isInteractive()) {
                $response = $style->confirm('Remove all of the above packages?', false);
            
                if (false === $response) {
                    return null;
                }
            }
        }
        
        return array_merge($extensionNames, $dependents);
    }
}
