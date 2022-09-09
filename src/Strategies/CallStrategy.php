<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithMethod;

/**
 * @extends ForwardStrategy<CallInteraction>
 */
class CallStrategy extends ForwardStrategy
{

    public function __construct(InteractsWithMethod $interaction)
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