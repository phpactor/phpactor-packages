<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Composer\Package\CompletePackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Util\PackageFilter;

class ExtensionLister
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Extension[]
     */
    public function list(): array
    {
        return array_map(function (CompletePackageInterface $package) {
            return Extension::fromPackage($package);
        }, PackageFilter::filter($this->repository->getPackages()));
    }
}
