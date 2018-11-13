<?php

namespace Phpactor\Extension\ExtensionManager\EventSubscriber;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Phpactor\Extension\ExtensionManager\Model\ExtensionFileGenerator;

class PostInstallSubscriber implements EventSubscriberInterface
{
    /**
     * @var ExtensionWriter
     */
    private $extensionWriter;

    public function __construct(ExtensionFileGenerator $extensionWriter)
    {
        $this->extensionWriter = $extensionWriter;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'handlePostPackageInstall',
            ScriptEvents::POST_UPDATE_CMD => 'handlePostPackageInstall',
        ];
    }

    public function handlePostPackageInstall(Event $event)
    {
        $repository = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $this->extensionWriter->writeExtensionList($repository->getPackages());
    }
}
