<?php

namespace Phpactor\BasePathResolver;

interface Filter
{
    public function apply(string $path): string;
}
