<?php

namespace Phpactor\Extension\WorseLanguageServer\Tests\Unit\Handler;

use LanguageServerProtocol\Location;
use LanguageServerProtocol\Position;
use LanguageServerProtocol\TextDocumentIdentifier;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Extension\WorseLanguageServer\Handler\GotoDefinitionHandler;
use Phpactor\LanguageServer\Core\Dispatcher\Handler;
use Phpactor\LanguageServer\Core\Rpc\ResponseMessage;
use Phpactor\LanguageServer\Core\Session\SessionManager;
use Phpactor\LanguageServer\Test\HandlerTester;
use Phpactor\ReferenceFinder\DefinitionLocation;
use Phpactor\ReferenceFinder\DefinitionLocator;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocumentBuilder;

class GotoDefinitionHandlerTest extends TestCase
{
    const EXAMPLE_URI = '/test';
    const EXAMPLE_TEXT = 'hello';


    /**
     * @var SessionManager
     */
    private $manager;
    /**
     * @var ObjectProphecy|DefinitionLocator
     */
    private $locator;

    /**
     * @var TextDocumentItem
     */
    private $document;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var TextDocumentIdentifier
     */
    private $identifier;

    public function setUp()
    {
        $this->manager = new SessionManager();
        $this->manager->initialize('foo');
        $this->locator = $this->prophesize(DefinitionLocator::class);

        $this->document = new TextDocumentItem();
        $this->document->uri = __FILE__;
        $this->document->text = self::EXAMPLE_TEXT;
        $this->manager->current()->workspace()->open($this->document);
        $this->identifier = new TextDocumentIdentifier(__FILE__);
        $this->position = new Position(1, 1);
    }

    public function testGoesToDefinition()
    {
        $document = TextDocumentBuilder::create(self::EXAMPLE_TEXT)
            ->language('php')
            ->uri(__FILE__)
            ->build()
        ;

        $this->locator->locateDefinition(
            $document,
            ByteOffset::fromInt(7)
        )->willReturn(
            new DefinitionLocation($document->uri(), ByteOffset::fromInt(2))
        );

        $tester = new HandlerTester(new GotoDefinitionHandler($this->manager, $this->locator->reveal()));
        $responses = $tester->dispatch('textDocument/definition', [
            'textDocument' => $this->identifier,
            'position' => $this->position,
        ]);
        $this->assertInstanceOf(ResponseMessage::class, $responses[0]);
        $location = $responses[0]->result;
        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals('file://' . __FILE__, $location->uri);
        $this->assertEquals(2, $location->range->start->character);
    }
}
