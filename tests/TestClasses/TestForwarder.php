<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Interaction;

class TestForwarder extends Forwarder
{

    public function forwardCall(object $target, string $method, array $parameters): mixed
    {
        return parent::forwardCall($target, $method, $parameters); 
    }
    
    public function forwardGet(object $target, string $property): mixed
    {
        return parent::forwardGet($target, $property); 
    }

    public function forwardSet(object $target, string $property, mixed $value): void
    {
        parent::forwardSet($target, $property, $value); 
    }
    
    public function forwardToTarget(Interaction $interaction)
    {
        return parent::forwardToTarget($interaction); 
    }
    
    public function notifyInterceptors(Interaction $interaction): void
    {
        parent::notifyInterceptors($interaction); 
    }

}