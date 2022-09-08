<?php

namespace Test\TestClasses;

use Closure;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class TestForwardStrategy extends ForwardStrategy
{

    private ?Closure $doExecute;

    public function __construct(TestInteraction $interaction = new TestInteraction(), Closure $doExecute = null)
    {
        parent::__construct($interaction);

        $this->doExecute = $doExecute;
    }

    public function setDoExecute(?Closure $doExecute): void
    {
        $this->doExecute = $doExecute;
    }

    protected function doExecute()
    {
        if ($this->doExecute) {
            return ($this->doExecute)($this->interaction);
        }
    }

}