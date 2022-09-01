<?php

use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{

    public function test_proxy_returns_proxy_when_value_from_forwarding_call_is_object(): void
    {
        $subject = new class {

            public function test(): stdClass
            {
                return new stdClass();
            }

        };

        $proxy = new Proxy($subject);

        $this->assertInstanceOf(
            Proxy::class,
            $proxy->test()
        );
    }

    public function test_proxy_returns_proxy_when_value_from_forwarding_property_is_object(): void
    {
        $subject = new class {

            public object $test;

            public function __construct()
            {
                $this->test = new stdClass();
            }

        };

        $proxy = new Proxy($subject);

        $this->assertInstanceOf(
            Proxy::class,
            $proxy->test
        );
    }

    public function test_proxy_returns_expected_value_when_forwarding_call(): void
    {
        $subject = new class {

            public function test(): string
            {
                return 'expected';
            }

        };

        $proxy = new Proxy($subject);

        $this->assertEquals(
            'expected',
            $proxy->test()
        );
    }

    public function test_proxy_returns_expected_value_when_accessing_property(): void
    {
        $subject = new class {

            public string $test = 'expected';

        };

        $proxy = new Proxy($subject);

        $this->assertEquals(
            'expected',
            $proxy->test
        );
    }

    public function test_proxy_returns_derived_class_when_wrapping(): void
    {
        $proxy = new class(new stdClass()) extends Proxy {

            public function getWrapped()
            {
                return parent::wrapIfValueIsObject($this->subject);
            }

        };

//        die(get_class($proxy) . '   ' . get_class($proxy->getWrapped()));

        $this->assertEquals(
            get_class($proxy),
            get_class($proxy->getWrapped())
        );
    }

}