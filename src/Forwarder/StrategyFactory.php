<?php

namespace JesseGall\Proxy\Forwarder;

use JesseGall\Proxy\Forwarder\Strategies\CallStrategy;
use JesseGall\Proxy\Forwarder\Strategies\GetStrategy;
use JesseGall\Proxy\Forwarder\Strategies\SetStrategy;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InvokesMethod;
use JesseGall\Proxy\Interactions\Contracts\MutatesProperty;
use JesseGall\Proxy\Interactions\Contracts\RetrievesProperty;

class StrategyFactory
{

    public function make(Interacts $interaction, object $caller = null): ?Strategy
    {
        if ($interaction instanceof InvokesMethod) {
            return new CallStrategy($interaction, $caller);
        }

        if ($interaction instanceof MutatesProperty) {
            return new SetStrategy($interaction, $caller);
        }

        if ($interaction instanceof RetrievesProperty) {
            return new GetStrategy($interaction, $caller);
        }

        return null;
    }

}