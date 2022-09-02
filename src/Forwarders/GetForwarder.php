<?php

namespace JesseGall\Proxy\Forwarders;

class GetForwarder extends Forwarder
{

    protected function forward(object $target, ...$args): mixed
    {
        [$property] = $args;

        return $target->{$property};
    }
}