<?php

namespace JesseGall\Proxy\Interceptors;

use Closure;

class Interceptor
{

    protected ?Closure $handler;

    public function __construct(Closure $handler = null)
    {
        $this->handler = $handler;
    }

    public function __invoke(object $target, ...$args): mixed
    {
        if (! $this->handler) {
            return null;
        }

        return ($this->handler)($target, ...$args);
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
     * @return Interceptor
     */
    public function setHandler(?Closure $handler): Interceptor
    {
        $this->handler = $handler;

        return $this;
    }

}