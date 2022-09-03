<?php

namespace Test;

use JesseGall\Proxy\Interactions\Call;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestTarget;

class CallTest extends TestCase
{

    public function test_set_and_get_method_change_and_return_expected_value()
    {
        $call = new Call(new TestTarget(), 'initialMethod', []);

        $call->setMethod($expected = 'newMethod');

        $this->assertEquals($expected, $call->getMethod());
    }

    public function test_set_and_get_parameters_change_and_return_expected_value()
    {
        $call = new Call(new TestTarget(), 'method', [0, 0, 0]);

        $call->setParameters($expected = [1, 2, 3]);

        $this->assertEquals($expected, $call->getParameters());
    }

}