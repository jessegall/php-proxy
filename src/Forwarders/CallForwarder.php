<?php

namespace JesseGall\Proxy\Forwarders;

class CallForwarder extends Forwarder
{

    protected function forward(object $target, ...$args): mixed
    {
        [$method, $parameters] = $args;

        return $target->{$method}(...$parameters);
    }
}