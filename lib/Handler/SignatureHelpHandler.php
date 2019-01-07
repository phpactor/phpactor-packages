<?php

namespace Phpactor\Extension\LanguageServerCompletion\Handler;

use Generator;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\ServerCapabilities;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\SignatureHelpOptions;
use LanguageServerProtocol\TextDocumentIdentifier;
use Phpactor\Extension\LanguageServerCompletion\Model\Signature\CouldNotHelp;
use Phpactor\Extension\LanguageServerCompletion\Model\Signature\SignatureHelpProvider;
use Phpactor\LanguageServer\Core\Handler\CanRegisterCapabilities;
use Phpactor\LanguageServer\Core\Handler\Handler;
use Phpactor\LanguageServer\Core\Session\Workspace;

class SignatureHelpHandler implements Handler, CanRegisterCapabilities
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
        TextDocumentIdentifier $textDocument,
        Position $position
    ): Generator
    {
        $document = $this->workspace->get($textDocument->uri);

        try {
            yield $this->provider->provideHelp($document, $position);
        } catch (CouldNotHelp $couldNotHelp) {
            yield new SignatureHelp();
        }
    }

    public function registerCapabiltiies(ServerCapabilities $capabilities)
    {
        $options = new SignatureHelpOptions();
        $options->triggerCharacters = [ '(', ',' ];
        $capabilities->signatureHelpProvider = $options;
    }
}
