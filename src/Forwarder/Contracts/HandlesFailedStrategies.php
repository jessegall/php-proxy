<?php

namespace JesseGall\Proxy\Forwarder\Contracts;

use JesseGall\Proxy\Contracts\Handler;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

interface HandlesFailedStrategies extends Handler
{

    public function handle(ExecutionException $exception): void;

}