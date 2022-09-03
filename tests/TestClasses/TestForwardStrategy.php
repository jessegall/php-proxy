<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Strategies\ForwardStrategy;

/**
 * @extends ForwardStrategy<TestInteraction>
 */
class TestForwardStrategy extends ForwardStrategy
{

    public function __construct(TestInteraction $interaction)
    {
        parent::__construct($interaction);
    }

    protected function doExecute()
    {
        return $this->interaction->callback();
    }

}