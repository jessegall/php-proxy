<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

interface HandlesFailedStrategies
{

    public function handle(ExecutionException $exception): void;

}