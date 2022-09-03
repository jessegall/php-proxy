<?php

namespace Test\TestClasses;

use Closure;
use Test\Concerns\LogsMethodCalls;

class TestTarget
{
    use LogsMethodCalls;

    public string $get = 'expected';
    public string $set = 'initial';

    public function call(Closure $closure = null)
    {
        $this->logMethodCall(__FUNCTION__);

        if ($closure) {
            return ($closure)();
        }

        return 'expected';
    }
}