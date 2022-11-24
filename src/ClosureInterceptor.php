<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

/**
 * @extends ClosureDelegate<\JesseGall\Proxy\Contracts\Intercepts>
 */
class ClosureInterceptor extends ClosureDelegate implements Intercepts
{

    public function intercept(Interacts $interaction, object $caller = null): void
    {
        $this->call($interaction, $caller);
    }

}