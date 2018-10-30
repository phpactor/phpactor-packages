<?php

namespace Phpactor\FilePathResolver\Filter;

use Phpactor\FilePathResolver\Expander;
use Phpactor\FilePathResolver\Filter;

class TokenExpandingFilter implements Filter
{
    /**
     * @var Expander[]
     */
    private $expanders = [];

    public function __construct(array $expanders = [])
    {
        foreach ($expanders as $expander) {
            $this->add($expander);
        }
    }

    public function apply(string $path): string
    {
        foreach ($this->expanders as $key => $expander) {
            if (false === strpos($path, $key)) {
                continue;
            }

            $path = str_replace($key, $expander->replacementValue(), $path);
        }

        return $path;
    }

    private function add(Expander $expander)
    {
        $this->expanders[$expander->tokenName()] = $expander;
    }
}
