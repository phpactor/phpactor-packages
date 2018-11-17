<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;

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
