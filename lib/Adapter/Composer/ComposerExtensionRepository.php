<?php

namespace Phpactor\Extension\ExtensionManager\Adapter\Composer;

use Composer\Package\AliasPackage;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionState;
use Phpactor\Extension\ExtensionManager\Model\Extensions;
use RuntimeException;

class ComposerExtensionRepository implements ExtensionRepository
{
    private const TYPE = 'phpactor-extension';

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var RepositoryInterface
     */
    private $primaryRepository;

    /**
     * @var RepositoryInterface
     */
    private $packagistRepository;

    public function __construct(
        RepositoryInterface $repository,
        RepositoryInterface $primaryRepository,
        RepositoryInterface $packagistRepository
    ) {
        $this->repository = $repository;
        $this->primaryRepository = $primaryRepository;
        $this->packagistRepository = $packagistRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function installedExtensions(): Extensions
    {
        return new Extensions(array_map(function (CompletePackageInterface $package) {
            return Extension::fromPackage(
                $package,
                $this->extensionState($package)
            );
        }, self::filter($this->repository->getPackages())));
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): Extensions
    {
        $packages = $this->packagistRepository->search('', 0, 'phpactor-extension');

        $allExtensions = new Extensions(array_map(function (array $packageInfo) {
            $package = $this->packagistRepository->findPackage($packageInfo['name'], '*');

            return Extension::fromPackage(
                $package,
                $this->extensionState($package),
                $this->has($package->getName())
            );
        }, $packages));

        return $this->installedExtensions()->merge($allExtensions);
    }

    public function find(string $extension): Extension
    {
        $package = $this->findPackage($extension);

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

        return Extension::fromPackage($package, $this->extensionState($package));
    }

    public function has(string $extension): bool
    {
        return null !== $this->findPackage($extension, '*');
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

    private function belongsToPrimaryRepository(CompletePackageInterface $package): bool
    {
        return null !== $this->primaryRepository->findPackage($package->getName(), '*');
    }

    private function findPackage(string $extension): ?PackageInterface
    {
        return $this->repository->findPackage($extension, '*');
    }

    private function extensionState(PackageInterface $package)
    {
        if ($this->belongsToPrimaryRepository($package)) {
            return ExtensionState::STATE_PRIMARY;
        }

        if ($this->has($package->getName())) {
            return ExtensionState::STATE_SECONDARY;
        }

        return ExtensionState::STATE_NOT_INSTALLED;
    }
}
