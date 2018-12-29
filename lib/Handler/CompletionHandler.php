<?php

namespace Phpactor\Extension\WorseLanguageServer\Handler;

use Generator;
use LanguageServerProtocol\CompletionItem;
use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\Diagnostic;
use LanguageServerProtocol\DiagnosticSeverity;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\Range;
use LanguageServerProtocol\TextDocumentItem;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Extension\LanguageServer\Helper\OffsetHelper;
use Phpactor\Extension\WorseLanguageServer\Util\PhpactorToLspCompletionType;
use Phpactor\LanguageServer\Core\Dispatcher\Handler;
use Phpactor\LanguageServer\Core\Rpc\NotificationMessage;
use Phpactor\LanguageServer\Core\Session\SessionManager;
use Phpactor\WorseReflection\Core\Reflector\SourceCodeReflector;

class CompletionHandler implements Handler
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
     * @var SourceCodeReflector
     */
    private $reflector;

    public function __construct(SessionManager $sessionManager, Completor $completor, SourceCodeReflector $reflector)
    {
        $this->completor = $completor;
        $this->sessionManager = $sessionManager;
        $this->reflector = $reflector;
    }

    public function methods(): array
    {
        return [
            'textDocument/completion' => 'completion',
        ];
    }

    public function completion(TextDocumentItem $textDocument, Position $position): Generator
    {
        $textDocument = $this->sessionManager->current()->workspace()->get($textDocument->uri);

        $suggestions = $this->completor->complete(
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

        $diagnostics = $this->resolveDiagnostics($textDocument, $position);

        yield new NotificationMessage('textDocument/publishDiagnostics', [
            'uri' => $textDocument->uri,
            'diagnostics' => $diagnostics
        ]);
    }

    private function resolveDiagnostics(TextDocumentItem $textDocument, Position $position)
    {
        $reflectionOffset = $this->reflector->reflectOffset(
            substr($textDocument->text, 0, $position->toOffset($textDocument->text)),
            $position->toOffset($textDocument->text)
        );
        
        $issues = $reflectionOffset->symbolContext()->issues();
        $diagnostics = [];
        $position = $reflectionOffset->symbolContext()->symbol()->position();

        if ($issues) {
            $diagnostics[] = new Diagnostic(
                implode(', ', $issues),
                new Range(
                    OffsetHelper::offsetToPosition($textDocument->text, $position->start()),
                    OffsetHelper::offsetToPosition($textDocument->text, $position->end())
                ),
                null,
                DiagnosticSeverity::WARNING,
                'phpactor'
            );
        }

        return $diagnostics;
    }
}
