<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Phpactor\Extension\ExtensionManager\Model\DependentExtensionFinder;
use RuntimeException;

class DepdendentExtensionFinder
{
    private $repository;

    public function __construct(ExtensionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Extension[]
     */
    public function findDependentExtensions(array $extensions): array
    {
        $resolved = [];
        foreach ($extensions as $extension) {
            $this->repository->find($extension);
            $resolved = array_merge($this->findDependentPackages($extension), $resolved);
        }

        return $resolved;
    }

    /**
     * @return Extension[]
     */
    private function findDependentPackages(string $sourcePackage, array $dependents = []): array
    {
        foreach ($this->repository->extensions() as $extension) {
            if (isset($dependents[$extension->name()])) {
                continue;
            }

            foreach ($extension->dependencies() as $dependency) {
                if ($dependency !== $sourcePackage) {
                    continue;
                }

                $dependents[$extension->name()] = $extension;
                $dependents = $this->findDependentPackages($extension->name(), $dependents);
            }
        }

        return $dependents;
    }
}
