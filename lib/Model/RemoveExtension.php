<?php

namespace Phpactor\Extension\ExtensionManager\Model;

interface RemoveExtension
{
    public function remove(string $extension): bool;
}
