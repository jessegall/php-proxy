<?php

namespace JesseGall\Proxy\Forwarder;

use JesseGall\Proxy\Forwarder\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

/**
 * @extends ClosureDelegate<\JesseGall\Proxy\Forwarder\Contracts\HandlesFailedStrategies>
 */
class ClosureHandler extends ClosureDelegate implements HandlesFailedStrategies
{

    public function handle(ExecutionException $exception): void
    {
        $this->call($exception);
    }

}