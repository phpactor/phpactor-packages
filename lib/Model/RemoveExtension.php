<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionSelector;
use Composer\Repository\RepositoryInterface;
use RuntimeException;

interface RemoveExtension
{
    public function remove(string $extension): bool;
}
