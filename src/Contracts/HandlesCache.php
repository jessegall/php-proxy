<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

interface HandlesCache
{

    public function store(ConcludedInteraction $concluded): void;

    public function get(Interacts $interaction): ConcludedInteraction;

    public function has(Interacts $interaction): bool;

}