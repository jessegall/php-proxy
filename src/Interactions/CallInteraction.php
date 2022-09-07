<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasMethodAndParameters;
use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithMethod;

class CallInteraction extends Interaction implements InteractsWithMethod, InteractsAndReturnsResult
{
    use HasResult, HasMethodAndParameters;

    public function __construct(object $target, string $method, array $parameters)
    {
        parent::__construct($target);

        $this->method = $method;
        $this->parameters = $parameters;
    }

}