<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesCache;

class Cache implements HandlesCache
{

    /**
     * The concluded interactions
     *
     * @var ConcludedInteraction[]
     */
    protected array $interactions = [];

    public function put(string $key, ConcludedInteraction $interaction)
    {
        $this->interactions[$key] = $interaction;
    }

    public function get(string $key): ConcludedInteraction
    {
        return $this->interactions[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->interactions);
    }
}