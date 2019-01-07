<?php

namespace Phpactor\Extension\LanguageServerCompletion\Signature;

use LanguageServerProtocol\Position;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\TextDocumentItem;

interface SignatureHelpProvider
{
    public function provideHelp(TextDocumentItem $item, Position $position): SignatureHelp;
}
