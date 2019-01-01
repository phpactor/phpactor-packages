<?php

namespace Phpactor\Extension\LanguageServerCompletion\Handler;

use Generator;
use LanguageServerProtocol\CompletionItem;
use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\CompletionOptions;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\Range;
use LanguageServerProtocol\ServerCapabilities;
use LanguageServerProtocol\SignatureHelpOptions;
use LanguageServerProtocol\TextDocumentItem;
use LanguageServerProtocol\TextEdit;
use Microsoft\PhpParser\LineCharacterPosition;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Completion\Core\TypedCompletorRegistry;
use Phpactor\Extension\LanguageServerCompletion\Util\PhpactorToLspCompletionType;
use Phpactor\Extension\LanguageServer\Helper\OffsetHelper;
use Phpactor\LanguageServer\Core\Dispatcher\Handler;
use Phpactor\LanguageServer\Core\Event\EventSubscriber;
use Phpactor\LanguageServer\Core\Event\LanguageServerEvents;
use Phpactor\LanguageServer\Core\Session\SessionManager;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocumentBuilder;

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

        $languageId = $textDocument->languageId ?: 'php';
        $suggestions = $this->registry->completorForType(
            $languageId
        )->complete(
            TextDocumentBuilder::create($textDocument->text)->language($languageId)->uri($textDocument->uri)->build(),
            ByteOffset::fromInt($position->toOffset($textDocument->text))
        );

        $completionList = new CompletionList();
        $completionList->isIncomplete = true;

        foreach ($suggestions as $suggestion) {
            /** @var Suggestion $suggestion */
            $completionList->items[] = new CompletionItem(
                $suggestion->name(),
                PhpactorToLspCompletionType::fromPhpactorType($suggestion->type()),
                $suggestion->shortDescription(),
                null,
                null,
                null,
                null,
                $this->textEdit($suggestion, $textDocument)
            );
        }

        yield $completionList;
    }

    public function capabilities(ServerCapabilities $capabilities): void
    {
        $capabilities->completionProvider = new CompletionOptions(false, [':', '>']);
        $capabilities->signatureHelpProvider = new SignatureHelpOptions(['(', ',']);
    }

    private function textEdit(Suggestion $suggestion, TextDocumentItem $textDocument): ?TextEdit
    {
        return $suggestion->range() ? new TextEdit(
            new Range(
                OffsetHelper::offsetToPosition($textDocument->text, $suggestion->range()->start()->toInt()),
                OffsetHelper::offsetToPosition($textDocument->text, $suggestion->range()->end()->toInt())
            ),
            $suggestion->name()
        ) : null;
    }
}
