<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Composer\Package\Version\VersionSelector;
use Composer\Repository\RepositoryInterface;
use Generator;
use RuntimeException;

interface AddExtension
{
    public function add($extension): string;
}

