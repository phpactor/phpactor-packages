<?php

namespace Phpactor\Extension\LanguageServerCompletion\Tests\Unit;

use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\TextDocumentIdentifier;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\LanguageServerCompletion\LanguageServerCompletionExtension;
use Phpactor\Extension\LanguageServerCompletion\Tests\IntegrationTestCase;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\LanguageServer\Core\Rpc\ResponseMessage;
use Phpactor\LanguageServer\LanguageServerBuilder;
use Phpactor\LanguageServer\Test\ServerTester;

class LanguageServerCompletionExtensionTest extends IntegrationTestCase
{
    public function testComplete()
    {
        $tester = $this->createTester();
        $tester->initialize();

        $document = new TextDocumentItem();
        $document->uri = '/test';
        $document->text = 'hello';
        $position = new Position(1, 1);
        $tester->openDocument($document);

        $responses = $tester->dispatch('textDocument/completion', [
            'textDocument' => $document,
            'position' => $position,
        ]);
        $response = $responses[0];

        $this->assertInstanceOf(ResponseMessage::class, $response);
        $this->assertNull($response->responseError);
        $this->assertInstanceOf(CompletionList::class, $response->result);
    }

    public function testSignatureProvider()
    {
        $tester = $this->createTester();
        $tester->initialize();

        $document = new TextDocumentItem();
        $document->uri = '/test';
        $document->text = 'hello';
        $position = new Position(1, 1);
        $tester->openDocument($document);
        $identifier = new TextDocumentIdentifier($document->uri);

        $responses = $tester->dispatch('textDocument/signatureHelp', [
            'textDocument' => $identifier,
            'position' => $position,
        ]);
        $response = $responses[0];

        $this->assertInstanceOf(ResponseMessage::class, $response);
        $this->assertNull($response->responseError);
        $this->assertInstanceOf(SignatureHelp::class, $response->result);
    }
}
