<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;

class FailedAction
{

    /**
     * The thrown exception
     *
     * @var Exception
     */
    protected readonly Exception $exception;

    /**
     * The failed action
     *
     * @var Closure
     */
    protected readonly Closure $action;

    /**
     * The caller of the failed action
     *
     * @var Proxy
     */
    protected readonly Proxy $caller;

    public function __construct(Exception $exception, Closure $action, Proxy $caller)
    {
        $this->exception = $exception;
        $this->action = $action;
        $this->caller = $caller;
    }

    /**
     * @return Exception
     */
    public function getException(): Exception
    {
        return $this->exception;
    }

    /**
     * @return Closure
     */
    public function getAction(): Closure
    {
        return $this->action;
    }

    /**
     * @return Proxy
     */
    public function getCaller(): Proxy
    {
        return $this->caller;
    }

}