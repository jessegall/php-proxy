<?php

namespace Test\TestClasses;

use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\InterceptorContract;
use Test\Concerns\LogsMethodCalls;

class TestForwarder extends Forwarder
{
    use LogsMethodCalls;

    public function forward(Interaction $interaction): ConcludedInteraction
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return new ConcludedInteraction($interaction->setStatus(Status::FULFILLED));
    }

    protected function forwardToTarget(Interaction $interaction): mixed
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return parent::forwardToTarget($interaction);
    }

    protected function forwardCall(object $target, string $method, array $parameters): mixed
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return parent::forwardCall($target, $method, $parameters);
    }

    protected function forwardGet(object $target, string $property): mixed
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return parent::forwardGet($target, $property);
    }

    protected function forwardSet(object $target, string $property, mixed $value): mixed
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return parent::forwardSet($target, $property, $value);
    }

    public function addInterceptor(InterceptorContract $interceptor): void
    {
        $this->logMethodCall(__FUNCTION__);

        parent::addInterceptor($interceptor);
    }

    public function getInterceptors(): array
    {
        if ($result = $this->logMethodCall(__FUNCTION__)) {
            return $result;
        }

        return parent::getInterceptors();
    }

    public function setInterceptors(array $interceptors): Forwarder
    {
        $this->logMethodCall(__FUNCTION__);

        return parent::setInterceptors($interceptors);
    }

}