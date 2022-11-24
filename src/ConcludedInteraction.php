<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\Status;

/**
 * @template T of Interaction
 */
class ConcludedInteraction
{

    /**
     * The concluded interaction
     *
     * @var T
     */
    protected readonly Interacts $interaction;

    /**
     * The time at which the interaction was concluded
     *
     * @var float
     */
    protected readonly float $timestamp;

    /**
     * The caller of the interaction
     *
     * @var object|null
     */
    private ?object $caller;

    /**
     * @var bool
     */
    private bool $fromCache;

    /**
     * @param T $interaction
     */
    public function __construct(Interacts $interaction, object $caller = null, bool $fromCache = false)
    {
        $this->interaction = $interaction;
        $this->timestamp = microtime(true);
        $this->caller = $caller;
        $this->fromCache = $fromCache;
    }

    /**
     * @return T
     */
    public function getInteraction(): Interacts
    {
        return $this->interaction;
    }

    /**
     * @return object|null
     */
    public function getCaller(): ?object
    {
        return $this->caller;
    }

    /**
     * @return T
     */
    public function getTarget(): object
    {
        return $this->interaction->getTarget();
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->interaction->getStatus();
    }

    /**
     * @return float
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getResult(): mixed
    {
        if ($this->interaction instanceof InteractsAndReturnsResult) {
            return $this->interaction->getResult();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isFromCache(): bool
    {
        return $this->fromCache;
    }


}