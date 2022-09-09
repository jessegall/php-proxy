<?php

namespace Test\Unit;

use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Strategies\CallStrategy;
use JesseGall\Proxy\Strategies\GetStrategy;
use Test\TestCase;

class CallStrategyTest extends TestCase
{

    public function test_methods_of_the_target_is_correctly_called()
    {
        $target = new class {
            public function method(int $a, int $b): string
            {
                return $a + $b;
            }
        };

        $strategy = new CallStrategy(new CallInteraction($target, 'method', [2, 3]));

        $this->assertEquals(5, invade($strategy)->doExecute());
    }

}