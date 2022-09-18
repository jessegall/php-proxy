<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class TestForwarder extends Forwarder
{

    public function newStrategy(Interacts $interaction): ForwardStrategy
    {
        return parent::newStrategy($interaction);
    }

    public function tryExecuting(ForwardStrategy $strategy): void
    {
        parent::tryExecuting($strategy);
    }

    public function notifyInterceptors(Interacts $interaction, object $caller = null): void
    {
        parent::notifyInterceptors($interaction, $caller);
    }

}