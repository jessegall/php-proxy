<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Contracts\InteractsWithMethod;

/**
 * @extends ForwardStrategy<\JesseGall\Proxy\Interactions\CallInteraction>
 */
class CallStrategy extends ForwardStrategy
{

    public function __construct(InteractsWithMethod $interaction, object $caller = null)
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