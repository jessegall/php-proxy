<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Interaction;

interface InterceptorContract
{

    public function intercept(Interaction $interaction): void;

}