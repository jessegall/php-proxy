<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestForwardStrategy;
use Test\TestClasses\TestInteraction;

class ForwardStrategyTest extends TestCase
{

    public function test_can_get_interaction()
    {
        $strategy = new TestForwardStrategy($expected = new TestInteraction());

        $this->assertEquals($expected, $strategy->getInteraction());
    }

    public function test_can_get_and_set_result()
    {
        $strategy = new TestForwardStrategy();

        $strategy->setResult($expected = 'expected');

        $this->assertEquals($expected, $strategy->getResult());
    }

}