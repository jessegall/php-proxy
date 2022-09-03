<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class TestForwarder extends Forwarder
{

    public function newForwardStrategy(Interaction $interaction): ForwardStrategy
    {
        return parent::newForwardStrategy($interaction);
    }

    public function try(ForwardStrategy $strategy): mixed
    {
        return parent::try($strategy);
    }

    public function notifyInterceptors(Interaction $interaction): void
    {
        parent::notifyInterceptors($interaction); 
    }

}