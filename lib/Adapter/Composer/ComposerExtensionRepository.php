<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\AliasPackage;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use RuntimeException;

class ComposerExtensionRepository implements ExtensionRepository
{
    private const TYPE = 'phpactor-extension';

    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return array_map(function (CompletePackageInterface $package) {
            return Extension::fromPackage($package);
        }, self::filter($this->repository->getPackages()));
    }

    public function find(string $extension): Extension
    {
        $package = $this->repository->findPackage($extension, '*');

        if (!$package) {
            throw new RuntimeException(sprintf(
                'Could not find package "%s"',
                $extension
            ));
        }

        if ($package->getType() !== self::TYPE) {
            throw new RuntimeException(sprintf(
                'Package is not a "%s" type, it is a "%s"',
                self::TYPE,
                $package->getType()
            ));
        }

        if (!$package instanceof CompletePackageInterface) {
            throw new RuntimeException(sprintf(
                'Package must be a complete package, got "%s"',
                get_class($package)
            ));
        }

        return Extension::fromPackage($package);
    }

    /**
     * @param PackageInterface[] $packages
     * @return CompletePackageInterface[]
     */
    private static function filter(array $packages): array
    {
        return array_filter($packages, function (PackageInterface $package) {
            return
                $package->getType() === self::TYPE &&
                !$package instanceof AliasPackage &&
                $package instanceof CompletePackageInterface;
        });
    }
}
