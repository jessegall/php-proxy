<?php

namespace JesseGall\Proxy\Strategies;


use JesseGall\Proxy\Interactions\SetInteraction;

/**
 * @extends ForwardStrategy<SetInteraction>
 */
class ForwardSet extends ForwardStrategy
{

    protected function doExecute(): void
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();
        $value = $this->interaction->getValue();

        $target->{$property} = $value;
    }

}