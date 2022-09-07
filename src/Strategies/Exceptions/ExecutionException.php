<?php

namespace JesseGall\Proxy\Strategies\Exceptions;

use Exception;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class ExecutionException extends Exception
{

    private ForwardStrategy $strategy;
    private Exception $exception;

    public function __construct(ForwardStrategy $strategy, Exception $exception)
    {
        parent::__construct("Execution of forward strategy failed");

        $this->strategy = $strategy;
        $this->exception = $exception;
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

}