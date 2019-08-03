<?php

namespace Phpactor\ReferenceFinder;

use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocumentUri;

final class DefinitionLocation
{
    /**
     * @var TextDocumentUri
     */
    private $uri;

    /**
     * @var ByteOffset
     */
    private $byteOffset;

    public function __construct(TextDocumentUri $uri, ByteOffset $byteOffset)
    {
        $this->uri = $uri;
        $this->byteOffset = $byteOffset;
    }

    public function uri(): TextDocumentUri
    {
        return $this->uri;
    }

    public function offset(): ByteOffset
    {
        return $this->byteOffset;
    }
}
