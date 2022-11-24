<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\ResolvesForwardStrategy;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class ForwardStrategyResolver implements ResolvesForwardStrategy
{

    /**
     * @var class-string<\JesseGall\Proxy\Strategies\ForwardStrategy>
     */
    private string $strategyType;

    /**
     * @var class-string<\JesseGall\Proxy\Strategies\ForwardStrategy>
     */
    public function __construct(string $strategyType)
    {
        $this->strategyType = $strategyType;
    }

    public function resolve(Interacts $interaction, object $caller = null): ForwardStrategy
    {
        return new $this->strategyType($interaction, $caller);
    }

}