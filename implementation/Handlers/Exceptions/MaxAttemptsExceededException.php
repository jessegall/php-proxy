<?php

namespace JesseGall\Proxy\Implementation\Handlers\Exceptions;

use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

class MaxAttemptsExceededException extends \RuntimeException
{

    private ExecutionException $exception;

    public function __construct(ExecutionException $exception)
    {
        parent::__construct("Max attempts exceeded");

        $this->exception = $exception;
    }

    /**
     * @return ExecutionException
     */
    public function getException(): ExecutionException
    {
        return $this->exception;
    }

}