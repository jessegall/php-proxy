<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

class TestExceptionHandler implements HandlesFailedStrategies
{

    private ?\Closure $callback;

    public function __construct(\Closure $callback = null)
    {
        $this->callback = $callback;
    }

    public function handle(ExecutionException $exception): void
    {
        if ($this->callback) {
            ($this->callback)($exception);
        }
    }

}