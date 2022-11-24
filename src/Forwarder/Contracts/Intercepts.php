<?php

namespace JesseGall\Proxy\Forwarder\Contracts;

use JesseGall\Proxy\Interactions\Contracts\Interacts;

interface Intercepts
{

    /**
     * @param Interacts $interaction
     * @param object|null $caller
     * @return void
     */
    public function intercept(Interacts $interaction, object $caller = null): void;

}