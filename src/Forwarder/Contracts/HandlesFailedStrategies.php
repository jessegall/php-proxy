<?php

namespace JesseGall\Proxy\Forwarder\Contracts;

use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

interface HandlesFailedStrategies
{

    public function handle(ExecutionException $exception): void;

}