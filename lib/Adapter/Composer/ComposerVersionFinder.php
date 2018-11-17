<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\Version\VersionSelector;
use Phpactor\Extension\ExtensionManager\Model\VersionFinder;
use RuntimeException;

class ComposerVersionFinder implements VersionFinder
{
    /**
     * @var VersionSelector
     */
    private $selector;

    public function __construct(VersionSelector $selector)
    {
        $this->selector = $selector;
    }

    public function findBestVersion(string $extensionName): string
    {
        $package = $this->selector->findBestCandidate($extensionName);

        if (is_bool($package)) {
            throw new RuntimeException(sprintf(
                'Could not find suitable version for extension "%s"',
                $extensionName
            ));
        }

        return $package->getPrettyVersion();
    }
}
