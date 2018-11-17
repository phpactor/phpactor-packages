<?php

namespace Phpactor\Extension\ExtensionManager\Model;

interface ExtensionRepository
{
    /**
     * @return Extension[]
     */
    public function extensions(): array;

    public function find(string $extension): Extension;

    public function has(string $extension): bool;
}
