<?php

namespace Test\Unit;

use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Strategies\GetStrategy;
use Test\TestCase;

class GetStrategyTest extends TestCase
{

    public function test_value_of_the_target_is_correctly_returned()
    {
        $target = new class {
            public string $property = 'expected';
        };

        $strategy = new GetStrategy(new GetInteraction($target, 'property'));

        $this->assertEquals('expected', invade($strategy)->doExecute());
    }

}