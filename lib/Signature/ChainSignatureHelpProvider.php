<?php

namespace Phpactor\Extension\LanguageServerCompletion\Signature;

use LanguageServerProtocol\Position;
use LanguageServerProtocol\SignatureHelp;
use LanguageServerProtocol\TextDocumentItem;
use Psr\Log\LoggerInterface;

class ChainSignatureHelpProvider implements SignatureHelpProvider
{
    /**
     * @var SignatureHelpProvider[]
     */
    private $providers = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, array $providers)
    {
        foreach ($providers as $provider) {
            $this->add($provider);
        }
        $this->logger = $logger;
    }

    public function provideHelp(
        TextDocumentItem $item,
        Position $position
    ): SignatureHelp
    {
        foreach ($this->providers as $provider) {
            try {
                return $provider->provideHelp($item, $position);
            } catch (CouldNotHelp $couldNotHelp) {
                $this->logger->debug(sprintf(
                    'Could not provide signature: "%s"', $couldNotHelp->getMessage()
                ));
            }
        }

        throw new CouldNotHelp(
            'Could not provide signature with chain provider'
        );
    }

    private function add(SignatureHelpProvider $provider)
    {
        $this->providers[] = $provider;
    }
}
