<?php

namespace Phpactor\Extension\LanguageServerReferenceFinder\Tests\Unit;

use LanguageServerProtocol\InitializeResult;
use LanguageServerProtocol\TextDocumentIdentifier;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\LanguageServerReferenceFinder\LanguageServerReferenceFinderExtension;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\LanguageServer\Core\Rpc\RequestMessage;
use Phpactor\LanguageServer\Core\Rpc\ResponseError;
use Phpactor\LanguageServer\Core\Rpc\ResponseMessage;
use Phpactor\LanguageServer\LanguageServerBuilder;

class LanguageServerCompletionExtensionTest extends TestCase
{
    public function testComplete()
    {
        $container = PhpactorContainer::fromExtensions([
            LoggingExtension::class,
            LanguageServerExtension::class,
            LanguageServerReferenceFinderExtension::class,
            ReferenceFinderExtension::class,
        ]);

        $builder = $container->get(LanguageServerExtension::SERVICE_LANGUAGE_SERVER_BUILDER);
        $this->assertInstanceOf(LanguageServerBuilder::class, $builder);
        $dispatcher = $builder->buildDispatcher();
        $responses = $dispatcher->dispatch(new RequestMessage(1, 'initialize', [
            'rootUri' => __DIR__
        ]));
        $responses = iterator_to_array($responses);
        $response = $responses[0];
        $this->assertInstanceOf(ResponseMessage::class, $response);
        $this->assertNull($response->responseError);
        $this->assertInstanceOf(InitializeResult::class, $response->result);

        $responses = $dispatcher->dispatch(new RequestMessage(1, 'textDocument/didOpen', [
            'textDocument' => new TextDocumentItem(__FILE__, 'php', 1, file_get_contents(__FILE__)),
        ]));
        $responses = iterator_to_array($responses);
        $response = $responses[0];

        $responses = $dispatcher->dispatch(new RequestMessage(1, 'textDocument/definition', [
            'textDocument' => new TextDocumentIdentifier(__FILE__),
            'position' => [
            ],
        ]));
        $responses = iterator_to_array($responses);
        $response = $responses[0];
        $this->assertInstanceOf(ResponseError::class, $response->responseError);
        $this->assertContains('Unable to locate definition', $response->responseError->message);
    }
}
