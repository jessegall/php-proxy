<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\Interactions\Contracts\Interacts;

interface Intercepts
{

    public function intercept(Interacts $interaction, object $interactor = null): void;

}