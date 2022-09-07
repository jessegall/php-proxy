<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Strategies\ForwardStrategy;

/**
 * @extends ForwardStrategy<TestInteraction>
 */
class TestForwardStrategy extends ForwardStrategy
{

    public function __construct(TestInteraction $interaction = null)
    {
        parent::__construct($interaction ?: new TestInteraction());
    }

    public function doExecute()
    {
        return $this->interaction->callback();
    }

}