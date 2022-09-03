<?php

namespace Test\TestClasses;

use Closure;
use Test\Concerns\LogsMethodCalls;

class TestTarget
{
    use LogsMethodCalls;

    public string $get = 'expected';
    public string $set = 'initial';

    public function call(...$args)
    {
        $this->logMethodCall(__FUNCTION__);

        if (($closure = $args[0] ?? null) && $closure instanceof Closure) {
            return ($closure)();
        }

        return 'expected';
    }
}