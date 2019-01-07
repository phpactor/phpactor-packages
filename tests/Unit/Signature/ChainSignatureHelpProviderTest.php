<?php

namespace Phpactor\Extension\LanguageServerCompletion\Tests\Unit\Signature;

use LanguageServerProtocol\Position;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Extension\LanguageServerCompletion\Model\Signature\ChainSignatureHelpProvider;
use Phpactor\Extension\LanguageServerCompletion\Model\Signature\CouldNotHelp;
use Phpactor\Extension\LanguageServerCompletion\Model\Signature\SignatureHelpProvider;
use Psr\Log\LoggerInterface;

class ChainSignatureHelpProviderTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $logger;
    /**
     * @var ObjectProphecy
     */
    private $provider1;
    /**
     * @var TextDocumentItem
     */
    private $item;
    /**
     * @var Position
     */
    private $position;
    /**
     * @var ObjectProphecy
     */
    private $help;

    public function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->provider1 = $this->prophesize(SignatureHelpProvider::class);

        $this->item = new TextDocumentItem('file:///foo','php',1,'foo');
        $this->position = new Position(1, 2);
        $this->help = $this->prophesize(SignatureHelp::class);
    }

    public function testNoProvidersThrowsException()
    {
        $this->expectException(CouldNotHelp::class);
        $this->create([])->provideHelp($this->item, $this->position);
    }

    public function testProviderCouldNotHelp()
    {
        $this->expectException(CouldNotHelp::class);
        $this->provider1->provideHelp($this->item, $this->position)->willThrow(new CouldNotHelp('Foobar'));
        $this->logger->debug('Could not provide signature: "Foobar"')->shouldBeCalled();

        $this->create([
            $this->provider1->reveal(),
        ])->provideHelp($this->item, $this->position);
    }

    public function testProvidersSignature()
    {
        $this->provider1->provideHelp($this->item, $this->position)->willReturn($this->help->reveal());

        $help = $this->create([
            $this->provider1->reveal(),
        ])->provideHelp($this->item, $this->position);

        $this->assertSame($this->help->reveal(), $help);
    }

    private function create(array $providers)
    {
        return new ChainSignatureHelpProvider($this->logger->reveal(), $providers);
    }
}
