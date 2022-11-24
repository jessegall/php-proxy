<?php

namespace JesseGall\Proxy\Forwarder\Strategies;


use JesseGall\Proxy\Interactions\Contracts\MutatesProperty;

/**
 * @extends Strategy<\JesseGall\Proxy\Interactions\SetInteraction>
 */
class SetStrategy extends Strategy
{

    public function __construct(MutatesProperty $interaction, object $caller = null)
    {
        parent::__construct($interaction, $caller);
    }

    protected function doExecute(): void
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();
        $value = $this->interaction->getValue();

        $target->{$property} = $value;
    }

}