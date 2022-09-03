<?php

namespace JesseGall\Proxy;

use Exception;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class FailedExecution
{

    /**
     * The failed strategy
     *
     * @var ForwardStrategy
     */
    protected readonly ForwardStrategy $strategy;

    /**
     * The thrown exception
     *
     * @var Exception
     */
    protected readonly Exception $exception;

    public function __construct(ForwardStrategy $strategy, Exception $exception)
    {
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