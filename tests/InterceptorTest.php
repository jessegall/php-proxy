<?php

namespace Test;

use JesseGall\Proxy\Interceptors\Interceptor;
use PHPUnit\Framework\TestCase;
use stdClass;

class InterceptorTest extends TestCase
{

    public function test_null_is_returned_when_handler_is_not_set()
    {
        $interceptor = new Interceptor();

        $this->assertNull($interceptor(new stdClass()));
    }

    public function test_get_handler_returns_current_handler()
    {
        $interceptor = new Interceptor($handler = fn() => 'test');

        $this->assertEquals($handler, $interceptor->getHandler());
    }

}