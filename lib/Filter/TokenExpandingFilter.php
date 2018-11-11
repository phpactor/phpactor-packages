<?php

namespace Phpactor\FilePathResolver\Filter;

use Phpactor\FilePathResolver\Expanders;
use Phpactor\FilePathResolver\Filter;

class TokenExpandingFilter implements Filter
{
    /**
     * @var Expanders
     */
    private $expanders;

    public function __construct(Expanders $expanders)
    {
        $this->expanders = $expanders;
    }

    public function apply(string $path): string
    {
        foreach ($this->expanders as $key => $expander) {
            $key = '%' . $key . '%';
            if (false === strpos($path, $key)) {
                continue;
            }

            $path = str_replace($key, $expander->replacementValue(), $path);
        }

        return $path;
    }
}
