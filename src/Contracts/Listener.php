<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\ConcludedInteraction;

interface Listener extends Handler
{

    public function handle(ConcludedInteraction $interaction): void;


}