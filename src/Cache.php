<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesCache;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Interaction;

class Cache implements HandlesCache
{

    protected array $interactions = [];

    public function store(ConcludedInteraction $concluded): void
    {
        $this->interactions[$concluded->getInteraction()->toHash()] = $concluded;
    }

    public function get(Interacts $interaction): ConcludedInteraction
    {
        return $this->interactions[$interaction->toHash()];
    }

    public function has(Interacts $interaction): bool
    {
        return array_key_exists($interaction->toHash(), $this->interactions);
    }
}