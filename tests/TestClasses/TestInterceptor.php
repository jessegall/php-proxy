<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\InterceptorContract;

class TestInterceptor implements InterceptorContract
{

    public function intercept(Interaction $interaction): void
    {
        //
    }

}