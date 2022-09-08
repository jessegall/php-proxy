<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Interactions\Interaction;

class TestInteraction extends Interaction
{

    public function __construct(TestTarget $target = new TestTarget())
    {
        parent::__construct($target);
    }

}