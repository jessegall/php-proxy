<?php

namespace JesseGall\Proxy\Strategies\Exceptions;

use Exception;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class ExecutionException extends Exception
{

    protected ForwardStrategy $strategy;
    protected Exception $exception;
    protected bool $shouldThrow;


    public function __construct(ForwardStrategy $strategy, Exception $exception)
    {
        parent::__construct("Execution of forward strategy failed");

        $this->strategy = $strategy;
        $this->exception = $exception;
        $this->shouldThrow = true;
    }

    /**
     * @return ForwardStrategy
     */
    public function getStrategy(): ForwardStrategy
    {
        return $this->strategy;
    }

    /**
     * @return Exception
     */
    public function getException(): Exception
    {
        return $this->exception;
    }

    /**
     * @return bool
     */
    public function shouldThrow(): bool
    {
        return $this->shouldThrow;
    }

    /**
     * @param bool $throw
     * @return ExecutionException
     */
    public function setShouldThrow(bool $throw): ExecutionException
    {
        $this->shouldThrow = $throw;
        return $this;
    }

}