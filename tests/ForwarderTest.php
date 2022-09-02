<?php

namespace Test;

use JesseGall\Proxy\Forwarders\Forwarder;
use JesseGall\Proxy\Interceptors\Interceptor;
use PHPUnit\Framework\TestCase;
use stdClass;

class ForwarderTest extends TestCase
{

    public function test_expected_value_is_returned_when_interceptor_is_not_set()
    {
        $forwarder = new class() extends Forwarder {
            protected function forward(...$args): mixed
            {
                return 'expected';
            }
        };

        $this->assertEquals(
            'expected',
            $forwarder(new stdClass())
        );
    }

    public function test_expected_value_is_returned_when_interceptor_does_not_return_value()
    {
        $forwarder = new class(function () {
            // Do nothing
        }) extends Forwarder {
            protected function forward(...$args): mixed
            {
                return 'expected';
            }
        };

        $this->assertEquals(
            'expected',
            $forwarder(new stdClass())
        );
    }

    public function test_expected_value_is_returned_when_interceptor_returns_value()
    {
        $forwarder = new class(function () {
            return 'expected';
        }) extends Forwarder {
            protected function forward(...$args): mixed
            {
                return 'invalid';
            }
        };

        $this->assertEquals(
            'expected',
            $forwarder(new stdClass())
        );
    }

    public function test_interceptor_handler_can_be_replaced_with_new_handler()
    {
        $forwarder = new class(function () {
            return 'invalid';
        }) extends Forwarder {
            protected function forward(...$args): mixed
            {
                return 'invalid';
            }
        };

        $forwarder->setInterceptor(fn() => 'expected');

        $this->assertEquals(
            'expected',
            $forwarder(new stdClass())
        );
    }

    public function test_interceptor_can_be_replaced_with_new_interceptor()
    {
        $forwarder = new class(function () {
            return 'invalid';
        }) extends Forwarder {
            protected function forward(...$args): mixed
            {
                return 'invalid';
            }
        };

        $forwarder->setInterceptor(new Interceptor(fn() => 'expected'));

        $this->assertEquals(
            'expected',
            $forwarder(new stdClass())
        );
    }

}
