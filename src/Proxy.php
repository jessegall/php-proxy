<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesCache;
use JesseGall\Proxy\Forwarder\Forwarder;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\WithResult;
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
     * The decorate mode.
     * This determines when a result should be wrapped in a proxy.
     *
     * @var DecorateMode
     */
    protected DecorateMode $decorateMode;

    /**
     * Indicates if the cache is enabled.
     *
     * @var bool
     */
    protected bool $cacheEnabled;

    /**
     * The cache handler.
     *
     * @var Cache
     */
    protected HandlesCache $cacheHandler;

    /**
     * The interaction forwarder.
     *
     * @var Forwarder
     */
    protected Forwarder $forwarder;

    /**
     * The interaction history
     *
     * @var ConcludedInteraction[]
     */
    protected array $history;

    /**
     * T $target
     */
    public function __construct(object $target)
    {
        $this->target = $target;
        $this->parent = null;
        $this->decorateMode = DecorateMode::EQUALS;
        $this->cacheEnabled = false;
        $this->cacheHandler = new Cache();
        $this->forwarder = new Forwarder();
        $this->history = [];
    }

    /**
     * Intercept method calls directed at the target.
     * Forwards the interaction to the forwarder and log the interaction.
     * If the returned result is an object, return new proxy where the target is the result.
     *
     * @param string $method
     * @param array $parameters
     * @return Proxy|mixed
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
     */
    public function __set(string $property, mixed $value): void
    {
        $this->processInteraction(new SetInteraction($this->target, $property, $value));
    }

    /**
     * Process and log an interaction.
     * When cache is enabled, return the cached result if available.
     *
     * @param Interacts $interaction
     * @return mixed
     */
    protected function processInteraction(Interacts $interaction): mixed
    {
        if ($this->cacheEnabled && $this->cacheHandler->has($interaction)) {
            $cached = $this->cacheHandler->get($interaction);

            $concluded = new ConcludedInteraction($cached->getInteraction(), $cached->getCaller(), true);
        } else {
            $concluded = $this->forwarder->forward($interaction, $this->getCaller());

            if ($this->cacheEnabled) {
                $this->cacheHandler->put($concluded);
            }
        }

        $this->logInteraction($concluded);

        if ($concluded->getInteraction() instanceof WithResult) {
            return $this->decorateResult($concluded);
        }

        return null;
    }

    /**
     * Wraps the result of a concluded interaction when the value is an object
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
        $this->history[] = $concluded;
    }

    /**
     * Returns the object that called the interaction
     *
     * @return object|null
     */
    protected function getCaller(): ?object
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10) as $trace) {
            $object = $trace['object'] ?? null;

            if ($object && $object != $this) {
                return $object;
            }
        }

        return null;
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
     * @return DecorateMode
     */
    public function getDecorateMode(): DecorateMode
    {
        return $this->decorateMode;
    }

    /**
     * @param DecorateMode $decorateMode
     * @return Proxy
     */
    public function setDecorateMode(DecorateMode $decorateMode): Proxy
    {
        $this->decorateMode = $decorateMode;

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
     * @return bool
     */
    public function cacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * @param bool $cacheEnabled
     * @return Proxy
     */
    public function setCacheEnabled(bool $cacheEnabled): Proxy
    {
        $this->cacheEnabled = $cacheEnabled;

        return $this;
    }

    /**
     * @return HandlesCache
     */
    public function getCacheHandler(): HandlesCache
    {
        return $this->cacheHandler;
    }

    /**
     * @param HandlesCache $cacheHandler
     * @return Proxy
     */
    public function setCacheHandler(HandlesCache $cacheHandler): Proxy
    {
        $this->cacheHandler = $cacheHandler;

        return $this;
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @param array $history
     * @return Proxy
     */
    public function setHistory(array $history): Proxy
    {
        $this->history = $history;

        return $this;
    }

}