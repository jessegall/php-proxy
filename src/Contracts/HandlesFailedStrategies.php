<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;

interface HandlesFailedStrategies
{

    public function handle(ExecutionException $exception): void;

}