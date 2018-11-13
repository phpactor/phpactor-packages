<?php

namespace Phpactor\Extension\ExtensionManager\Model;

interface DependentExtensionFinder
{
     /**
      * @return string[]
      */
    public function findDependentExtensions(array $extensions): array;
}
