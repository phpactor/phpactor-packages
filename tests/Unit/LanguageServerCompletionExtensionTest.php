<?php

namespace Phpactor\Extension\LanguageServerCompletion\Tests\Unit;

use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\LanguageServerCompletion\LanguageServerCompletionExtension;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\LanguageServer\Core\Rpc\ResponseMessage;
use Phpactor\LanguageServer\LanguageServerBuilder;
use Phpactor\LanguageServer\Test\ServerTester;

class LanguageServerCompletionExtensionTest extends TestCase
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

    private function createTester(): ServerTester
    {
        $container = PhpactorContainer::fromExtensions([
            LoggingExtension::class,
            CompletionExtension::class,
            LanguageServerExtension::class,
            LanguageServerCompletionExtension::class,
            FilePathResolverExtension::class,
        ]);
        
        $builder = $container->get(LanguageServerExtension::SERVICE_LANGUAGE_SERVER_BUILDER);
        $this->assertInstanceOf(LanguageServerBuilder::class, $builder);

        return $builder->buildServerTester();
    }
}
