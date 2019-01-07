<?php

namespace Phpactor\Extension\LanguageServerCompletion\Handler;

use Generator;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\TextDocumentIdentifier;
use Phpactor\Extension\LanguageServerCompletion\Signature\SignatureHelpProvider;
use Phpactor\LanguageServer\Core\Handler\Handler;
use Phpactor\LanguageServer\Core\Session\Workspace;

class SignatureHelpHandler implements Handler
{
    /**
     * @var Workspace
     */
    private $workspace;

    /**
     * @var SignatureHelpProvider
     */
    private $provider;


    public function __construct(Workspace $workspace, SignatureHelpProvider $provider)
    {
        $this->workspace = $workspace;
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function methods(): array
    {
        return [
            'textDocument/signatureHelp' => 'signatureHelp'
        ];
    }

    public function signatureHelp(
        TextDocumentIdentifier $item,
        Position $position
    ): Generator
    {
        $document = $this->workspace->get($item->uri);

        yield $this->provider->provideHelp($document, $position);
    }
}
