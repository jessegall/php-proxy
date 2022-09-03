<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HasResult;
use JesseGall\Proxy\Interactions\Interaction;
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
    protected readonly Interaction $interaction;

    /**
     * The time at which the interaction was concluded
     *
     * @var float
     */
    protected readonly float $timestamp;

    /**
     * @param T $interaction
     */
    public function __construct(Interaction $interaction)
    {
        $this->interaction = $interaction;
        $this->timestamp = microtime(true);
    }

    /**
     * @return float
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
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
     * @param Status $status
     * @return bool
     */
    public function hasStatus(Status $status): bool
    {
        return $this->interaction->hasStatus($status);
    }

    /**
     * @return mixed
     */
    public function getResult(): mixed
    {
        if ($this->interaction instanceof HasResult) {
            return $this->interaction->getResult();
        }

        return null;
    }


}