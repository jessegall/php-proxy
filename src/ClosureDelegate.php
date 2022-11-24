<?php

namespace JesseGall\Proxy;

use Closure;

/**
 * @template T
 */
class ClosureDelegate
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
    public function call(mixed ...$args)
    {
        return ($this->closure)(...$args);
    }

}