<?php

namespace Phpactor\TextDocument;

use Phpactor\TextDocument\Exception\InvalidUriException;
use Webmozart\PathUtil\Path;

class TextDocumentUri
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $path;

    final private function __construct(string $scheme, string $path)
    {
        $this->scheme = $scheme;
        $this->path = $path;
    }

    public static function fromString(string $uri): self
    {
        /** @var array $components */
        $components = parse_url($uri);

        if (!isset($components['path'])) {
            throw new InvalidUriException(sprintf(
                'URI "%s" has no path component',
                $uri
            ));
        }

        if (false === Path::isAbsolute($uri)) {
            throw new InvalidUriException(sprintf(
                'URI must be absolute, got "%s"',
                $uri
            ));
        }

        return new self(
            $components['scheme'] ?? 'file',
            $components['path']
        );
    }

    public function __toString(): string
    {
        return sprintf('%s://%s', $this->scheme, $this->path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function scheme(): string
    {
        return $this->scheme;
    }
}
