<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\DependentExtensionFinder;
use RuntimeException;

class ComposerDepdendentPackageFinder implements DependentExtensionFinder
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findDependentExtensions(array $extensions): array
    {
        return $this->findDependentPackages(array_map(function (string $extensionName) {
            return $this->findPackage($extensionName)->getName();
        }, $extensions));
    }

    /**
     * @return string[]
     */
    private function findDependentPackages(array $sourcePackages): array
    {
        $dependents = [];

        foreach ($this->repository->getPackages() as $package) {
            foreach ($package->getRequires() as $require) {
                if (!in_array($require->getTarget(), $sourcePackages)) {
                    continue;
                }

                $dependents[$package->getName()] = $package->getName();
                $dependents = array_merge($dependents, $this->findDependentPackages([$package->getName()]));
            }
        }

        return $dependents;
    }

    private function findPackage(string $extension): PackageInterface
    {
        $package = $this->repository->findPackage($extension, '*');

        if (null === $package) {
            throw new RuntimeException(sprintf(
                'Could not find extension "%s"', $extension
            ));
        }

        return $package;
    }
}
