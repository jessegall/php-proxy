<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class TestForwarder extends Forwarder
{

    public function newStrategy(Interaction $interaction): ForwardStrategy
    {
        return parent::newStrategy($interaction);
    }

    public function tryExecuting(ForwardStrategy $strategy): void
    {
        parent::tryExecuting($strategy);
    }

    public function notifyInterceptors(Interaction $interaction, object $interactor = null): void
    {
        parent::notifyInterceptors($interaction, $interactor);
    }

}