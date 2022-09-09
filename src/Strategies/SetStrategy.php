<?php

namespace JesseGall\Proxy\Strategies;


use JesseGall\Proxy\Interactions\Contracts\InteractsWithProperty;
use JesseGall\Proxy\Interactions\SetInteraction;

/**
 * @extends ForwardStrategy<SetInteraction>
 */
class SetStrategy extends ForwardStrategy
{

    public function __construct(InteractsWithProperty $interaction)
    {
        parent::__construct($interaction);
    }

    protected function doExecute(): void
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();
        $value = $this->interaction->getValue();

        $target->{$property} = $value;
    }

}