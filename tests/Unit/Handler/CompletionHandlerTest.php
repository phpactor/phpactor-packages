<?php

namespace Phpactor\Extension\WorseLanguageServer\Tests\Unit\Handler;

use Generator;
use LanguageServerProtocol\CompletionItem;
use LanguageServerProtocol\CompletionList;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\Extension\WorseLanguageServer\Handler\CompletionHandler;
use Phpactor\LanguageServer\Core\Handler;
use Phpactor\LanguageServer\Core\Rpc\ResponseMessage;
use Phpactor\LanguageServer\Core\Session\Manager;
use Phpactor\LanguageServer\Core\Session\SessionManager;
use Phpactor\LanguageServer\Test\HandlerTester;
use Phpactor\WorseReflection\Core\Reflector\SourceCodeReflector;
use Phpactor\WorseReflection\ReflectorBuilder;

class CompletionHandlerTest extends TestCase
{
    /**
     * @var SessionManager
     */
    private $manager;

    /**
     * @var TextDocumentItem
     */
    private $document;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var SourceCodeReflector
     */
    private $reflector;


    public function setUp()
    {
        $this->manager = new SessionManager();
        $this->manager->initialize('foo');
        $this->document = new TextDocumentItem();
        $this->document->uri = 'test';
        $this->document->text = 'hello';
        $this->position = new Position(1, 1);

        $this->reflector = ReflectorBuilder::create()->build();

        $this->manager->current()->workspace()->open($this->document);
    }

    public function testHandleNoSuggestions()
    {
        $tester = $this->create([]);
        $responses = $tester->dispatch(
            'textDocument/completion',
            [
                'textDocument' => $this->document,
                'position' => $this->position
            ]
        );
        $this->assertInstanceOf(ResponseMessage::class, $responses[0]);
        $list = $responses[0]->result;
        $this->assertInstanceOf(CompletionList::class, $list);
        $this->assertEquals([], $list->items);
    }

    public function testHandleSuggestions()
    {
        $tester = $this->create([
            Suggestion::create('hello'),
            Suggestion::create('goodbye'),
        ]);
        $responses = $tester->dispatch(
            'textDocument/completion',
            [
                'textDocument' => $this->document,
                'position' => $this->position
            ]
        );
        $this->assertInstanceOf(ResponseMessage::class, $responses[0]);
        $list = $responses[0]->result;
        $this->assertInstanceOf(CompletionList::class, $list);
        $this->assertEquals([
            new CompletionItem('hello'),
            new CompletionItem('goodbye'),
        ], $list->items);
    }

    private function create(array $suggestions): HandlerTester
    {
        return new HandlerTester(new CompletionHandler($this->manager, new class($suggestions) implements Completor {
            private $suggestions;
            public function __construct(array $suggestions)
            {
                $this->suggestions = $suggestions;
            }

            public function complete(string $source, int $offset): Generator
            {
                foreach ($this->suggestions as $suggestion) {
                    yield $suggestion;
                }
            }
        }, $this->reflector));
    }
}
