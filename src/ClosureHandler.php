<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

/**
 * @extends ClosureDelegate<\JesseGall\Proxy\Contracts\HandlesFailedStrategies>
 */
class ClosureHandler extends ClosureDelegate implements HandlesFailedStrategies
{

    public function handle(ExecutionException $exception): void
    {
        $this->call($exception);
    }

}