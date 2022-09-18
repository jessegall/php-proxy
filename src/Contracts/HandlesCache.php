<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\ConcludedInteraction;

interface HandlesCache
{

    public function put(string $key, ConcludedInteraction $interaction);

    public function get(string $key): ConcludedInteraction;

    public function has(string $key): bool;

}