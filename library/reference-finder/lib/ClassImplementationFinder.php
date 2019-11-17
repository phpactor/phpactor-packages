<?php

namespace Phpactor\ReferenceFinder;

use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\Locations;
use Phpactor\TextDocument\TextDocument;

interface ClassImplementationFinder
{
    public function findImplementations(TextDocument $document, ByteOffset $byteOffset): Locations;
}
