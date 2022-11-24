<?php

namespace JesseGall\Proxy\Forwarder\Strategies;

use JesseGall\Proxy\Interactions\Contracts\InvokesMethod;

/**
 * @extends Strategy<\JesseGall\Proxy\Interactions\CallInteraction>
 */
class CallStrategy extends Strategy
{

    public function __construct(InvokesMethod $interaction, object $caller = null)
    {
        parent::__construct($interaction, $caller);
    }

    protected function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $method = $this->interaction->getMethod();
        $params = $this->interaction->getParameters();

        return $target->{$method}(...$params);
    }

}