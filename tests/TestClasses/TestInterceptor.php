<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

class TestInterceptor implements Intercepts
{

    public function intercept(Interacts $interaction, object $interactor = null): void
    {
        //
    }

}