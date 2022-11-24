<?php

namespace JesseGall\Proxy\Forwarder\Strategies\Exceptions;

use Exception;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;

class ExecutionException extends Exception
{

    protected Strategy $strategy;
    protected Exception $exception;
    protected bool $shouldThrow;


    public function __construct(Strategy $strategy, Exception $exception)
    {
        parent::__construct("Execution of forward strategy failed");

        $this->strategy = $strategy;
        $this->exception = $exception;
        $this->shouldThrow = true;
    }

    /**
     * @return Strategy
     */
    public function getStrategy(): Strategy
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
     * @param Exception $exception
     * @return ExecutionException
     */
    public function setException(Exception $exception): ExecutionException
    {
        $this->exception = $exception;

        return $this;
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