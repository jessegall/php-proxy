<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesCache;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

class Cache implements HandlesCache
{

    protected array $interactions = [];

    public function put(ConcludedInteraction $concluded): bool
    {
        $this->interactions[$concluded->getInteraction()->toHash()] = $concluded;

        return true;
    }

    public function get(Interacts $interaction): ConcludedInteraction
    {
        return $this->interactions[$interaction->toHash()];
    }

    public function has(Interacts $interaction): bool
    {
        return array_key_exists($interaction->toHash(), $this->interactions);
    }

    public function clear(): void
    {
        $this->interactions = [];
    }
}