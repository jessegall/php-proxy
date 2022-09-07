<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\GetInteraction;

/**
 * @extends ForwardStrategy<GetInteraction>
 */
class ForwardGet extends ForwardStrategy
{

    protected function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();

        return $target->{$property};
    }

}