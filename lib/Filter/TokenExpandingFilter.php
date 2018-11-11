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

    /**
     * @var string
     */
    private $delimiter;

    public function __construct(Expanders $expanders, string $delimiter = '%')
    {
        $this->expanders = $expanders;
        $this->delimiter = $delimiter;
    }

    public function apply(string $path): string
    {
        if (!preg_match_all('{%(.*?)%}', $path, $matches)) {
            return $path;
        }

        foreach ($matches[1] as $match) {
            $expander = $this->expanders->get($match);
            $path = str_replace('%' . $match . '%', $expander->replacementValue(), $path);
        }

        return $path;
    }
}
