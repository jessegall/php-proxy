<?php

namespace JesseGall\Proxy\Forwarder\Exceptions;

use JesseGall\Proxy\Interactions\Contracts\Interacts;

class StrategyNullException extends \RuntimeException
{

    public function __construct(Interacts $interacts)
    {
        parent::__construct("No strategy found for interaction of type " . get_class($interacts) . ".");
    }

}