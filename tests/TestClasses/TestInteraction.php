<?php

namespace Test\TestClasses;

use Closure;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;

class TestInteraction extends Interaction
{

    private ?Closure $callback;

    public function __construct(object $target = null, Closure $callback = null)
    {
        if ($target instanceof Closure) {
            $callback = $target;
            $target = null;
        }

        parent::__construct($target ?: new TestTarget());

        $this->callback = $callback;
    }

    public function callback()
    {
        if ($this->callback) {
            return ($this->callback)();
        } else {
            $this->setStatus(Status::FULFILLED);
        }
    }

    public function setCallback(?Closure $callback): TestInteraction
    {
        $this->callback = $callback;

        return $this;
    }

}