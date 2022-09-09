<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Contracts\InteractsWithProperty;
use JesseGall\Proxy\Interactions\GetInteraction;

/**
 * @extends ForwardStrategy<GetInteraction>
 */
class GetStrategy extends ForwardStrategy
{

    public function __construct(InteractsWithProperty $interaction)
    {
        parent::__construct($interaction);
    }

    protected function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();

        return $target->{$property};
    }

}