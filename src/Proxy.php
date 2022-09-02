<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;
use JesseGall\Proxy\Forwarders\CallForwarder;
use JesseGall\Proxy\Forwarders\GetForwarder;
use JesseGall\Proxy\Handlers\Catcher;

/**
 * @template T
 * @mixin T
 */
class Proxy
{
    /**
     * @var T
     */
    protected object $target;

    /**
     * @var CallForwarder
     */
    protected CallForwarder $callForwarder;

    /**
     * @var GetForwarder
     */
    protected GetForwarder $getForwarder;

    /**
     * @var Catcher
     */
    protected Catcher $catcher;

    /**
     * T $subject
     */
    public function __construct(object $target)
    {
        $this->target = $target;

        $this->callForwarder = new CallForwarder();

        $this->getForwarder = new GetForwarder();

        $this->catcher = new Catcher();
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->try(fn() => $this->decorateIfIsObject(
            $this->forwardCall($method, $parameters)
        ));
    }

    /**
     * @param string $property
     * @return mixed
     * @throws Exception
     */
    public function __get(string $property): mixed
    {
        return $this->try(fn() => $this->decorateIfIsObject(
            $this->forwardGet($property)
        ));
    }

    /**
     * @return object
     */
    public function getTarget(): object
    {
        return $this->target;
    }

    /**
     * @param object $target
     * @return Proxy
     */
    public function setTarget(object $target): static
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return CallForwarder
     */
    public function getCallForwarder(): CallForwarder
    {
        return $this->callForwarder;
    }

    /**
     * @param CallForwarder $callForwarder
     * @return Proxy
     */
    public function setCallForwarder(CallForwarder $callForwarder): static
    {
        $this->callForwarder = $callForwarder;

        return $this;
    }

    /**
     * @return GetForwarder
     */
    public function getGetForwarder(): GetForwarder
    {
        return $this->getForwarder;
    }

    /**
     * @param GetForwarder $getForwarder
     * @return Proxy
     */
    public function setGetForwarder(GetForwarder $getForwarder): static
    {
        $this->getForwarder = $getForwarder;

        return $this;
    }

    /**
     * @return Catcher
     */
    public function getCatcher(): Catcher
    {
        return $this->catcher;
    }

    /**
     * @param Catcher|Closure $catcher
     * @return Proxy
     */
    public function setCatcher(Catcher|Closure $catcher): Proxy
    {
        if ($catcher instanceof Catcher) {
            $this->catcher = $catcher;
        } else {
            $this->catcher->setHandler($catcher);
        }

        return $this;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    protected function forwardCall(string $method, array $parameters): mixed
    {
        return $this->try($this->callForwarder, $this->target, $method, $parameters);
    }

    /**
     * @param string $property
     * @return mixed
     * @throws Exception
     */
    protected function forwardGet(string $property): mixed
    {
        return $this->try($this->getForwarder, $this->target, $property);
    }

    /**
     * @throws Exception
     */
    public function try(callable $callable, ...$args)
    {
        try {
            return $callable(...$args);
        } catch (Exception $exception) {
            return ($this->catcher)($exception, $callable, $this);
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function decorateIfIsObject(mixed $value): mixed
    {
        if (is_object($value)) {
            return $this->decorateObject($value);
        }

        return $value;
    }

    /**
     * @param object $value
     * @return Proxy
     */
    protected function decorateObject(object $value): Proxy
    {
        return (clone $this)->setTarget($value);
    }


}