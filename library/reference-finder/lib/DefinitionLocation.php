<?php

namespace Phpactor\ReferenceFinder;

use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\Location;
use Phpactor\TextDocument\TextDocumentUri;

/**
 * @deprecated Is replaced by the Phpactor\\TextDocument\\Location value object
 */
final class DefinitionLocation
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(TextDocumentUri $uri, ByteOffset $byteOffset)
    {
        $this->location = new Location($uri, $byteOffset);
    }

    public function uri(): TextDocumentUri
    {
        return $this->location->uri();
    }

    public function offset(): ByteOffset
    {
        return $this->location->offset();
    }
}
