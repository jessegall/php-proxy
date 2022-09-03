<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Contract\ReturnResultContract;
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

    public function addInterceptor(InterceptorContract $interceptor): void
    {
        $this->interceptors[] = $interceptor;
    }

    public function forward(Interaction $interaction): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction);

        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction);
        }

        $result = $this->forwardToTarget($interaction);

        if ($interaction instanceof ReturnResultContract) {
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

    protected function forwardToTarget(Interaction $interaction)
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

    protected function forwardSet(object $target, string $property, mixed $value): void
    {
        $target->{$property} = $value;
    }

    # --- Getters and Setters ---

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