<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Interactions\Interaction;

class TestInterceptor implements Intercepts
{

    public function intercept(Interaction $interaction): void
    {
        //
    }

}