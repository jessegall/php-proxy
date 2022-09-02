<?php

namespace Test;

use Exception;
use JesseGall\Proxy\Forwarders\CallForwarder;
use JesseGall\Proxy\Forwarders\GetForwarder;
use JesseGall\Proxy\Handlers\Catcher;
use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProxyTest extends TestCase
{

    public function test_expected_value_is_returned_on_call()
    {
        $proxy = new Proxy(new class {
            public function test()
            {
                return 'expected';
            }
        });

        $this->assertEquals(
            'expected',
            $proxy->test()
        );
    }

    public function test_expected_value_is_returned_on_get()
    {
        $proxy = new Proxy(new class {
            public string $test = 'expected';
        });

        $this->assertEquals(
            'expected',
            $proxy->test
        );
    }

    public function test_call_interceptor_handler_is_called()
    {
        $proxy = new Proxy(new class {
            public function test()
            {
                return 'invalid';
            }
        });

        $proxy->getCallForwarder()->getInterceptor()->setHandler(
            function () use (&$called) {
                $called++;
            }
        );

        $proxy->test();

        $this->assertEquals(
            1,
            $called
        );
    }

    public function test_call_interceptor_handler_is_called_with_correct_arguments()
    {
        $proxy = new Proxy($target = new class {
            public function test(): void
            {
                //
            }
        });

        $actual = [
            'target' => null,
            'method' => null,
            'parameters' => null,
        ];

        $proxy->getCallForwarder()->getInterceptor()->setHandler(
            function ($target, $method, $parameters) use (&$actual) {
                $actual['target'] = $target;
                $actual['method'] = $method;
                $actual['parameters'] = $parameters;
            }
        );

        $proxy->test('expected_one', 'expected_two');

        $this->assertEquals($target, $actual['target']);
        $this->assertEquals('test', $actual['method']);
        $this->assertEquals(['expected_one', 'expected_two'], $actual['parameters']);
    }

    public function test_get_interceptor_handler_is_called()
    {
        $proxy = new Proxy(new class {
            public string $test = 'test';
        });

        $proxy->getGetForwarder()->getInterceptor()->setHandler(
            function () use (&$called) {
                $called++;
            }
        );

        $value = $proxy->test;

        $this->assertEquals(
            1,
            $called
        );
    }

    public function test_value_is_wrapped_in_proxy_when_value_is_object()
    {
        $proxy = new Proxy(new class {
            public function test(): object
            {
                return new stdClass();
            }
        });

        $this->assertInstanceOf(Proxy::class, $proxy->test());
    }

    public function test_when_value_is_wrapped_the_proxy_is_correctly_cloned()
    {
        $proxy = new class extends Proxy {

            public function __construct()
            {
                parent::__construct(new stdClass());
            }

            public function decorateObject(object $value): Proxy
            {
                return parent::decorateObject($value);
            }
        };

        $proxy->setCallForwarder($callForwarder = new CallForwarder());
        $proxy->setGetForwarder($getForwarder = new GetForwarder());
        $proxy->setCatcher($catcher = new Catcher());

        $clone = $proxy->decorateObject($target = new stdClass());

        $this->assertEquals(get_class($proxy), get_class($clone));
        $this->assertEquals($callForwarder, $proxy->getCallForwarder());
        $this->assertEquals($getForwarder, $proxy->getGetForwarder());
        $this->assertEquals($catcher, $proxy->getCatcher());
        $this->assertEquals($target, $clone->getTarget());
    }

    public function test_catcher_handler_is_called_when_exception_is_thrown()
    {
        $proxy = new Proxy(new class {
            public function test()
            {
                throw new Exception();
            }
        });

        $proxy->setCatcher(function () use(&$called) {
            $called++;
        });

        $proxy->test();

        $this->assertEquals(1, $called);
    }

}