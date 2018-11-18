<?php

namespace Phpactor\Extension\ExtensionManager\Service;

use Phpactor\Extension\ExtensionManager\Model\Extension;
use Phpactor\Extension\ExtensionManager\Model\ExtensionRepository;
use Phpactor\Extension\ExtensionManager\Model\Extensions;

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

    public function list(): Extensions
    {
        return $this->repository->installedExtensions();
    }
}
