<?php

namespace Test\TestClasses;

use Closure;

class TestTarget
{

    public function call(...$args)
    {
        if (($closure = $args[0] ?? null) && $closure instanceof Closure) {
            return ($closure)();
        }

        return 'expected';
    }
}