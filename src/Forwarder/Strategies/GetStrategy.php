<?php

namespace JesseGall\Proxy\Forwarder\Strategies;

use JesseGall\Proxy\Interactions\Contracts\RetrievesProperty;
use JesseGall\Proxy\Interactions\GetInteraction;

/**
 * @extends Strategy<\JesseGall\Proxy\Interactions\GetInteraction>
 */
class GetStrategy extends Strategy
{

    public function __construct(RetrievesProperty $interaction, object $caller = null)
    {
        parent::__construct($interaction, $caller);
    }

    protected function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();

        return $target->{$property};
    }

}