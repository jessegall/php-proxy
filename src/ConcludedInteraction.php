<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Interaction;

/**
 * @template T of Interaction
 */
class ConcludedInteraction
{

    /**
     * The concluded interaction
     *
     * @var Interaction|mixed
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
     * @return T
     */
    public function getInteraction(): Interaction
    {
        return $this->interaction;
    }

    /**
     * @return float
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

}