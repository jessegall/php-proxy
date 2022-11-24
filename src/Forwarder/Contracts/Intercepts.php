<?php

namespace JesseGall\Proxy\Forwarder\Contracts;

use JesseGall\Proxy\Contracts\Handler;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

interface Intercepts extends Handler
{

    /**
     * @param Interacts $interaction
     * @param object|null $caller
     * @return void
     */
    public function handle(Interacts $interaction, object $caller = null): void;

}