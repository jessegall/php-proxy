<?php

namespace JesseGall\Proxy\Handlers;

use Closure;
use Exception;
use JesseGall\Proxy\Proxy;

class Catcher
{

    protected ?Closure $handler;

    public function __construct(Closure $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Exception $exception, callable $callable, Proxy $proxy): mixed
    {
        if (! $this->handler) {
            throw $exception;
        }

        return ($this->handler)($exception, $callable, $proxy);
    }

    /**
     * @return Closure|null
     */
    public function getHandler(): ?Closure
    {
        return $this->handler;
    }

    /**
     * @param Closure|null $handler
     * @return Catcher
     */
    public function setHandler(?Closure $handler): Catcher
    {
        $this->handler = $handler;

        return $this;
    }

}