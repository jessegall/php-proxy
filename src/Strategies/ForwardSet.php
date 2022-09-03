<?php

namespace JesseGall\Proxy\Strategies;


use JesseGall\Proxy\Interactions\Set;

/**
 * @extends ForwardStrategy<Set>
 */
class ForwardSet extends ForwardStrategy
{

    public function doExecute(): void
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();
        $value = $this->interaction->getValue();

        $target->{$property} = $value;
    }

}