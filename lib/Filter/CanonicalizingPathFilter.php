<?php

namespace Phpactor\BasePathResolver\Filter;

use Phpactor\BasePathResolver\Filter;
use Webmozart\PathUtil\Path;

class CanonicalizingPathFilter implements Filter
{
    public function apply(string $path): string
    {
        return Path::canonicalize($path);
    }
}
