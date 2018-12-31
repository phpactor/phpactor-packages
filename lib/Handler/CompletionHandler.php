<?php

namespace Phpactor\Extension\LanguageServerCompletion\Handler;

use Generator;
use LanguageServerProtocol\CompletionItem;
use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\CompletionOptions;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\ServerCapabilities;
use LanguageServerProtocol\SignatureHelpOptions;
use LanguageServerProtocol\TextDocumentItem;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Completion\Core\TypedCompletorRegistry;
use Phpactor\Extension\LanguageServerCompletion\Util\PhpactorToLspCompletionType;
use Phpactor\LanguageServer\Core\Dispatcher\Handler;
use Phpactor\LanguageServer\Core\Event\EventSubscriber;
use Phpactor\LanguageServer\Core\Event\LanguageServerEvents;
use Phpactor\LanguageServer\Core\Session\SessionManager;

class CompletionHandler implements Handler, EventSubscriber
{
    /**
     * @var Completor
     */
    private $completor;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var TypedCompletorRegistry
     */
    private $registry;

    public function __construct(SessionManager $sessionManager, TypedCompletorRegistry $registry)
    {
        $this->sessionManager = $sessionManager;
        $this->registry = $registry;
    }

    public function methods(): array
    {
        return [
            'textDocument/completion' => 'completion',
        ];
    }

    public function events(): array
    {
        return [
            LanguageServerEvents::CAPABILITIES_REGISTER => 'capabilities',
        ];
    }

    public function completion(TextDocumentItem $textDocument, Position $position): Generator
    {
        $textDocument = $this->sessionManager->current()->workspace()->get($textDocument->uri);

        $suggestions = $this->registry->completorForType(
            $textDocument->languageId ?: 'php'
        )->complete(
            $textDocument->text,
            $position->toOffset($textDocument->text)
        );

        $completionList = new CompletionList();
        $completionList->isIncomplete = true;

        foreach ($suggestions as $suggestion) {
            /** @var Suggestion $suggestion */
            $completionList->items[] = new CompletionItem(
                $suggestion->name(),
                PhpactorToLspCompletionType::fromPhpactorType($suggestion->type()),
                $suggestion->shortDescription()
            );
        }

        yield $completionList;
    }

    public function capabilities(ServerCapabilities $capabilities): void
    {
        $capabilities->completionProvider = new CompletionOptions(false, [':', '>']);
        $capabilities->signatureHelpProvider = new SignatureHelpOptions(['(', ',']);
    }
}
