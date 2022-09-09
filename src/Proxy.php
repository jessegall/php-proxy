<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Exceptions\ForwardStrategyMissingException;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\GetInteraction;
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
    protected ?Proxy $parent;

    /**
     * // TODO
     *
     * @var DecorateMode
     */
    protected DecorateMode $decorateMode;

    /**
     * // TODO
     *
     * @var bool
     */
    protected bool $cacheEnabled;

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
        $this->decorateMode = DecorateMode::EQUALS;
        $this->cacheEnabled = true;
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
        return $this->processInteraction(new CallInteraction($this->target, $method, $parameters));
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
        return $this->processInteraction(new GetInteraction($this->target, $property));
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
        $this->processInteraction(new SetInteraction($this->target, $property, $value));
    }

    /**
     * // TODO
     *
     * @throws ForwardStrategyMissingException
     */
    protected function processInteraction(Interacts $interaction): mixed
    {
        if ($this->isLogged($interaction)) {
            $concluded = $this->getLogged($interaction);
        } else {
            $concluded = $this->forwarder->forward($interaction);
        }

        $this->logInteraction($concluded);

        if ($concluded->getInteraction() instanceof InteractsAndReturnsResult) {
            return $this->decorateResult($concluded);
        }

        return null;
    }

    /**
     * // TODO
     *
     * @param ConcludedInteraction $interaction
     * @return mixed
     */
    protected function decorateResult(ConcludedInteraction $interaction): mixed
    {
        $result = $interaction->getResult();

        if (! is_object($result)) {
            return $result;
        }

        if ($this->decorateMode === DecorateMode::NEVER) {
            return $result;
        }

        if ($this->decorateMode === DecorateMode::EQUALS && $result !== $this->target) {
            return $result;
        }

        return (new static($result))->setForwarder($this->forwarder)->setParent($this);
    }

    /**
     * Log a concluded interaction.
     *
     * @param ConcludedInteraction $concluded
     * @return void
     */
    protected function logInteraction(ConcludedInteraction $concluded): void
    {
        $hash = $this->generateInteractionHash($concluded->getInteraction());

        $this->concludedInteractions[$hash] = $concluded;
    }

    /**
     * @param Interacts $interaction
     * @return bool
     */
    protected function isLogged(Interacts $interaction): bool
    {
        return array_key_exists($this->generateInteractionHash($interaction), $this->concludedInteractions);
    }

    /**
     * @param Interacts $interacts
     * @return ConcludedInteraction
     */
    protected function getLogged(Interacts $interaction): ConcludedInteraction
    {
        return $this->concludedInteractions[$this->generateInteractionHash($interaction)];
    }

    /**
     * Generates a hash for the given interaction
     *
     * @param Interacts $interaction
     * @return string
     */
    protected function generateInteractionHash(Interacts $interaction): string
    {
        return (new InteractionHash($interaction))->generate();
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