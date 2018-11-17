<?php

namespace Phpactor\Extension\ExtensionManager\Util;

use Composer\Package\AliasPackage;
use Composer\Package\PackageInterface;

class PackageFilter
{
    const TYPE = 'phpactor-extension';
    const EXTRA_EXTENSION_CLASS = 'phpactor.extension_class';

    /**
     * @param PackageInterface[] $packages
     */
    public static function filter(iterable $packages): array
    {
        $filtered = [];
        foreach ($packages as $package) {
            if ($package instanceof AliasPackage) {
                continue;
            }

            if ($package->getType() !== self::TYPE) {
                continue;
            }
            $filtered[] = $package;
        };

        return $filtered;
    }
}
