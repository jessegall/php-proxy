<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\CallInteraction;

/**
 * @extends ForwardStrategy<CallInteraction>
 */
class ForwardCall extends ForwardStrategy
{

    public function __construct(CallInteraction $interaction)
    {
        parent::__construct($interaction);
    }

    protected function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $method = $this->interaction->getMethod();
        $params = $this->interaction->getParameters();

        return $target->{$method}(...$params);
    }

}