<?php

namespace JesseGall\Proxy\Contracts;

use JesseGall\Proxy\Interactions\Interaction;

interface Intercepts
{

    public function intercept(Interaction $interaction, object $interactor = null): void;

}