<?php

namespace JesseGall\Proxy\Forwarder;

use JesseGall\Proxy\Forwarder\Contracts\Intercepts;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

/**
 * @extends ClosureDelegate<\JesseGall\Proxy\Forwarder\Contracts\Intercepts>
 */
class ClosureInterceptor extends ClosureDelegate implements Intercepts
{

    public function intercept(Interacts $interaction, object $caller = null): void
    {
        $this->call($interaction, $caller);
    }

}