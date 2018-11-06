<?php

namespace Phpactor\Extension\CompletionRpc\Handler;

use Phpactor\Completion\Core\Completor;
use Phpactor\Completion\Core\Suggestion;
use Phpactor\MapResolver\Resolver;
use Phpactor\Extension\Rpc\Handler;
use Phpactor\Extension\Rpc\Response\ReturnResponse;

class CompleteHandler implements Handler
{
    const NAME = 'complete';
    const PARAM_SOURCE = 'source';
    const PARAM_OFFSET = 'offset';

    /**
     * @var Completor
     */
    private $completor;

    public function __construct(Completor $compoletor)
    {
        $this->completor = $compoletor;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function configure(Resolver $resolver)
    {
        $resolver->setRequired([
            self::PARAM_SOURCE,
            self::PARAM_OFFSET,
        ]);
    }

    public function handle(array $arguments)
    {
        $suggestions = $this->completor->complete($arguments[self::PARAM_SOURCE], $arguments[self::PARAM_OFFSET]);
        $suggestions = array_map(function (Suggestion $suggestion) {
            return $suggestion->toArray();
        }, iterator_to_array($suggestions));

        return ReturnResponse::fromValue([
            'suggestions' => $suggestions,
            'issues' => [],
        ]);
    }
}
