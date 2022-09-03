<?php

namespace JesseGall\Proxy\Exceptions;

use JesseGall\Proxy\Interactions\Interaction;

class ForwardStrategyMissingException extends \Exception
{

    /**
     * @var Interaction
     */
    protected readonly Interaction $interaction;

    public function __construct(Interaction $interaction)
    {
        parent::__construct("Forward strategy missing for: " . get_class($interaction));

        $this->interaction = $interaction;
    }

    /**
     * @return Interaction
     */
    public function getInteraction(): Interaction
    {
        return $this->interaction;
    }

}