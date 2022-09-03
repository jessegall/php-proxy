<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Contract\ReturnsResultContract;
use JesseGall\Proxy\Interactions\Get;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Set;
use JesseGall\Proxy\Interactions\Status;

class Forwarder
{

    /**
     * @var InterceptorContract[]
     */
    protected array $interceptors = [];

    public function forward(Interaction $interaction): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction);

        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction);
        }

        $result = $this->forwardToTarget($interaction);

        if ($interaction instanceof ReturnsResultContract) {
            $interaction->setResult($result);
        }

        return new ConcludedInteraction($interaction);
    }

    protected function notifyInterceptors(Interaction $interaction): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->intercept($interaction);
        }
    }

    protected function forwardToTarget(Interaction $interaction): mixed
    {
        if ($interaction instanceof Call) {
            return $this->forwardCall(
                $interaction->getTarget(),
                $interaction->getMethod(),
                $interaction->getParameters()
            );
        }

        if ($interaction instanceof Get) {
            return $this->forwardGet(
                $interaction->getTarget(),
                $interaction->getProperty(),
            );
        }

        if ($interaction instanceof Set) {
            $this->forwardSet(
                $interaction->getTarget(),
                $interaction->getProperty(),
                $interaction->getValue(),
            );
        }
    }

    protected function forwardCall(object $target, string $method, array $parameters): mixed
    {
        return $target->{$method}(...$parameters);
    }

    protected function forwardGet(object $target, string $property): mixed
    {
        return $target->{$property};
    }

    protected function forwardSet(object $target, string $property, mixed $value): mixed
    {
        return $target->{$property} = $value;
    }

    /*
     * Getters and setters
     */

    public function addInterceptor(InterceptorContract $interceptor): void
    {
        $this->addInterceptor($interceptor);
    }

    public function getInterceptors(): array
    {
        return $this->interceptors;
    }

    public function setInterceptors(array $interceptors): Forwarder
    {
        $this->interceptors = $interceptors;

        return $this;
    }

}