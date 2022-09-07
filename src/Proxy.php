<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Exceptions\ForwardStrategyMissingException;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\SetInteraction;

/**
 * @template T
 * @mixin T
 */
class Proxy
{
    /**
     * The target of the proxy.
     *
     * @var T
     */
    protected object $target;

    /**
     * The parent proxy.
     *
     * @var Proxy|null
     */
    private ?Proxy $parent;

    /**
     * The list of concluded interactions.
     *
     * @var ConcludedInteraction[]
     */
    protected array $concludedInteractions;

    /**
     * The interaction forwarder of the proxy.
     *
     * @var Forwarder
     */
    protected Forwarder $forwarder;

    /**
     * T $subject
     */
    public function __construct(object $target)
    {
        $this->target = $target;
        $this->parent = null;
        $this->forwarder = new Forwarder();
        $this->concludedInteractions = [];
    }

    /**
     * Intercept method calls directed at the target.
     * Forwards the interaction to the forwarder and log the interaction.
     * If the returned result is an object, return new proxy where the target is the result.
     *
     * @param string $method
     * @param array $parameters
     * @return Proxy|mixed
     * @throws ForwardStrategyMissingException
     */
    public function __call(string $method, array $parameters): mixed
    {
        $concluded = $this->forwardAndLog(
            new CallInteraction($this->target, $method, $parameters)
        );

        return $this->decorateIfIsObject(
            $concluded->getResult()
        );
    }

    /**
     * Intercept attempts to access a property of the target.
     * Forwards the interaction to the forwarder and log the interaction.
     * If the returned result is an object, return new proxy where the target is the result.
     *
     * @param string $property
     * @return Proxy|mixed
     * @throws ForwardStrategyMissingException
     */
    public function __get(string $property): mixed
    {
        $concluded = $this->forwardAndLog(
            new GetInteraction($this->target, $property)
        );

        return $this->decorateIfIsObject(
            $concluded->getResult()
        );
    }

    /**
     * Intercept attempts to set a property value of the target.
     * Forwards the interaction to the forwarder and log the interaction.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws ForwardStrategyMissingException
     */
    public function __set(string $property, mixed $value): void
    {
        $this->forwardAndLog(
            new SetInteraction($this->target, $property, $value)
        );
    }

    /**
     * Forward the interaction to the forwarder and log the result
     *
     * @param Interaction $interaction
     * @return ConcludedInteraction
     * @throws ForwardStrategyMissingException
     */
    protected function forwardAndLog(Interaction $interaction): ConcludedInteraction
    {
        $concluded = $this->forwarder->forward($interaction);

        $this->logInteraction($concluded);

        return $concluded;
    }

    /**
     * Decorate the given value if value is an object.
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
     * Decorate the object by wrapping it in a new proxy.
     *
     * @param object $object
     * @return Proxy
     */
    protected function decorateObject(object $object): Proxy
    {
        $decorated = new static($object);

        $decorated->forwarder = $this->forwarder;

        $decorated->parent = $this;

        return $decorated;
    }

    /**
     * Log a concluded interaction.
     *
     * @param ConcludedInteraction $concluded
     * @return void
     */
    protected function logInteraction(ConcludedInteraction $concluded): void
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
    public function setTarget(object $target): static
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return Proxy|null
     */
    public function getParent(): ?Proxy
    {
        return $this->parent;
    }

    /**
     * @param Proxy|null $parent
     * @return Proxy
     */
    public function setParent(?Proxy $parent): static
    {
        $this->parent = $parent;

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
    public function setForwarder(Forwarder $forwarder): static
    {
        $this->forwarder = $forwarder;

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