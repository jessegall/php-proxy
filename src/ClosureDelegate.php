<?php

namespace JesseGall\Proxy;

use Closure;
use JesseGall\Proxy\Contracts\Handler;

/**
 * @template T
 */
class ClosureDelegate implements Handler
{

    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param mixed ...$args
     * @return T
     */
    public function handle(mixed ...$args)
    {
        return ($this->closure)(...$args);
    }

}