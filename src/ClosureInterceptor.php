<?php

namespace JesseGall\Proxy;

use Closure;
use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Interaction;

class ClosureInterceptor implements Intercepts
{

    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function intercept(Interacts $interaction, object $caller = null): void
    {
        ($this->closure)($interaction, $caller);
    }

    public function getClosure(): Closure
    {
        return $this->closure;
    }

}