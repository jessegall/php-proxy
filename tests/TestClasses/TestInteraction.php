<?php

namespace Test\TestClasses;

use Closure;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;

class TestInteraction extends Interaction
{

    private ?Closure $callback;

    public function __construct(object $target, Closure $callback = null)
    {
        parent::__construct($target);

        $this->setStatus(Status::FULFILLED);

        $this->callback = $callback;
    }

    public function callback()
    {
        if ($this->callback) {
            return ($this->callback)();
        }
    }


}