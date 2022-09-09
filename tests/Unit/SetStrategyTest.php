<?php

namespace Test\Unit;

use JesseGall\Proxy\Interactions\SetInteraction;
use JesseGall\Proxy\Strategies\SetStrategy;
use Test\TestCase;

class SetStrategyTest extends TestCase
{

    public function test_property_of_target_is_set_to_expected_value()
    {
        $target = new class {
            public string $property = 'initial';
        };

        $strategy = new SetStrategy(new SetInteraction($target, 'property', $expected = 'expected'));

        invade($strategy)->doExecute();

        $this->assertEquals($expected, $target->property);
    }

}