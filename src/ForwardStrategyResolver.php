<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\ResolvesForwardStrategy;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

class ForwardStrategyResolver implements ResolvesForwardStrategy
{

    /**
     * @var class-string<\JesseGall\Proxy\Forwarder\Strategies\Strategy>
     */
    private string $strategyType;

    /**
     * @var class-string<\JesseGall\Proxy\Forwarder\Strategies\Strategy>
     */
    public function __construct(string $strategyType)
    {
        $this->strategyType = $strategyType;
    }

    public function resolve(Interacts $interaction, object $caller = null): Strategy
    {
        return new $this->strategyType($interaction, $caller);
    }

}