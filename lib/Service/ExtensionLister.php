<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Composer\Package\CompletePackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Util\PackageFilter;

class ExtensionLister
{
    /**
     * @var ExtensionRepository
     */
    private $repository;

    public function __construct(ExtensionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Extension[]
     */
    public function list(): array
    {
        return $this->repository->extensions();
    }
}
