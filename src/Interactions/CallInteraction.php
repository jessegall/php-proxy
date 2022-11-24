<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasMethodAndParameters;
use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contracts\WithResult;
use JesseGall\Proxy\Interactions\Contracts\InvokesMethod;

class CallInteraction extends Interaction implements InvokesMethod
{
    use HasResult, HasMethodAndParameters;

    public function __construct(object $target, string $method, array $parameters)
    {
        parent::__construct($target);

        $this->method = $method;
        $this->parameters = $parameters;
    }

}