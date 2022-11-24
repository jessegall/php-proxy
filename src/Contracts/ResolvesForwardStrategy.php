<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Strategies\ForwardStrategy;

interface ResolvesForwardStrategy
{

    public function resolve(Interacts $interaction, object $caller = null): ForwardStrategy;

}