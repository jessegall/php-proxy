<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;
use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Get;
use JesseGall\Proxy\Interactions\Set;

/**
 * @template T
 * @mixin T
 */
class Proxy
{
    /**
     * The target of the proxy
     *
     * @var T
     */
    protected object $target;

    /**
     * The interaction forwarder of the proxy
     *
     * @var Forwarder
     */
    protected Forwarder $forwarder;

    /**
     * The exception handler of the proxy
     *
     * @var ExceptionHandler
     */
    protected ExceptionHandler $exceptionHandler;

    /**
     * A list of concluded interactions
     *
     * @var ConcludedInteraction[]
     */
    protected array $concludedInteractions;

    /**
     * T $subject
     */
    public function __construct(object $target)
    {
        $this->target = $target;
        $this->forwarder = new Forwarder();
        $this->exceptionHandler = new ExceptionHandler();
        $this->concludedInteractions = [];
    }

    /**
     * Intercept method calls directed at the target.
     * Forwards the interaction to the forwarder and registers the interaction.
     * If the returned result is an object, return new proxy where the target is the result.
     *
     * @param string $method
     * @param array $parameters
     * @return Proxy|mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->try(function () use ($method, $parameters) {
            $concluded = $this->forwardCall($method, $parameters);

            $this->registerConcludedInteraction($concluded);

            return $this->decorateIfIsObject(
                $concluded->getResult()
            );
        });
    }

    /**
     * Intercepts attempts to access a property of the target.
     * Forwards the interaction to the forwarder and registers the interaction.
     * If the returned result is an object, return new proxy where the target is the result.
     *
     * @param string $property
     * @return Proxy|mixed
     */
    public function __get(string $property): mixed
    {
        return $this->try(function () use ($property) {
            $concluded = $this->forwardGet($property);

            $this->registerConcludedInteraction($concluded);

            return $this->decorateIfIsObject(
                $concluded->getResult()
            );
        });
    }

    /**
     * Intercepts attempts to set a property value of the target.
     * Forwards the interaction to the forwarder and registers the interaction.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set(string $property, mixed $value): void
    {
        $this->try(function () use ($property, $value) {
            $concluded = $this->forwardSet($property, $value);

            $this->registerConcludedInteraction($concluded);
        });
    }

    /**
     * Forward a method call to the target using the forwarder
     *
     * @param string $method
     * @param array $parameters
     * @return ConcludedInteraction<Call>
     */
    protected function forwardCall(string $method, array $parameters): ConcludedInteraction
    {
        return $this->forwarder->forward(new Call($this->target, $method, $parameters));
    }

    /**
     * Forward accessing a property of the target using the forwarder
     *
     * @param string $property
     * @return ConcludedInteraction<Get>
     */
    protected function forwardGet(string $property): ConcludedInteraction
    {
        return $this->forwarder->forward(new Get($this->target, $property));
    }

    /**
     * Forward setting a property of the target using the forwarder
     *
     * @param string $property
     * @param mixed $value
     * @return ConcludedInteraction<Set>
     */
    protected function forwardSet(string $property, mixed $value): ConcludedInteraction
    {
        return $this->forwarder->forward(new Set($this->target, $property, $value));
    }

    /**
     * Attempt to run the given action.
     * Pass exceptions to the exception handler when thrown
     *
     * @param Closure $action
     * @return mixed
     */
    protected function try(Closure $action): mixed
    {
        try {
            return $action();
        } catch (Exception $exception) {
            return $this->exceptionHandler->handle(
                new FailedAction($exception, $action, $this)
            );
        }
    }

    /**
     * Decorate the given value if value is an object
     *
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
     * Decorate the object by wrapping it in a new proxy
     *
     * @param object $object
     * @return Proxy
     */
    protected function decorateObject(object $object): Proxy
    {
        return (clone $this)->setTarget($object);
    }

    /**
     * Register a concluded interaction
     *
     * @param ConcludedInteraction $concluded
     * @return void
     */
    protected function registerConcludedInteraction(ConcludedInteraction $concluded): void
    {
        $this->concludedInteractions[] = $concluded;
    }

    # --- Getters and Setters ---

    /**
     * @return object
     */
    public function getTarget(): object
    {
        return $this->target;
    }

    /**
     * @param object $target
     * @return $this
     */
    public function setTarget(object $target): Proxy
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return Forwarder
     */
    public function getForwarder(): Forwarder
    {
        return $this->forwarder;
    }

    /**
     * @param Forwarder $forwarder
     * @return $this
     */
    public function setForwarder(Forwarder $forwarder): Proxy
    {
        $this->forwarder = $forwarder;

        return $this;
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->exceptionHandler;
    }

    /**
     * @param ExceptionHandler $exceptionHandler
     * @return $this
     */
    public function setExceptionHandler(ExceptionHandler $exceptionHandler): Proxy
    {
        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

    /**
     * @return array
     */
    public function getConcludedInteractions(): array
    {
        return $this->concludedInteractions;
    }

}