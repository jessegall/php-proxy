<?php

namespace JesseGall\Proxy\Handlers;

use Closure;
use Exception;
use JesseGall\Proxy\ProxyRepeater;

class ProxyRepeaterHandler
{

    private ?Closure $handler;

    public function __construct(Closure $handler = null)
    {
        $this->handler = $handler;
    }

    public function __invoke(Exception $exception, ProxyRepeater $repeater): mixed
    {
        if (! $this->handler) {
            return true;
        }

        return ($this->handler)($exception, $repeater);
    }

}