<?php

namespace Tests;

use JesseGall\Proxy\Cache;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\DecorateMode;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Proxy;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;
use Tests\TestClasses\TestException;
use Tests\TestClasses\TestSubject;

class ProxyTest extends TestCase
{

    private TestSubject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new TestSubject();
    }

    public function test_can_get_property_through_proxy()
    {
        $proxy = new Proxy($this->subject);

        $this->assertEquals('value', $proxy->property);
    }

    public function test_can_set_property_through_proxy()
    {
        $proxy = new Proxy($this->subject);

        $proxy->property = 'new value';

        $this->assertEquals('new value', $this->subject->property);
    }

    public function test_can_invoke_method_through_proxy()
    {
        $proxy = new Proxy($this->subject);

        $this->assertEquals('value', $proxy->method());
    }

    public function test_interceptor_will_intercept_call_interaction()
    {
        $proxy = new Proxy($this->subject);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->method();

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_will_intercept_get_interaction()
    {
        $proxy = new Proxy($this->subject);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->property;

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_will_intercept_set_interaction()
    {
        $proxy = new Proxy($this->subject);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->property = 'new value';

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_can_cancel_interaction()
    {
        $proxy = new Proxy($this->subject);

        $proxy->getForwarder()->registerInterceptor(function (Interacts $interaction) {
            $interaction->setStatus(Status::CANCELLED);
        });

        $proxy->method();

        $this->assertEquals(0, $this->subject->called);
    }

    public function test_exception_handler_will_catch_exception()
    {
        $proxy = new Proxy($this->subject);

        $caught = false;

        $proxy->getForwarder()->registerExceptionHandler(function () use (&$caught) {
            $caught = true;
        });

        $this->expectException(TestException::class);

        $proxy->methodWithException();

        $this->assertTrue($caught);
    }

    public function test_exception_handler_can_cancel_exception()
    {
        $proxy = new Proxy($this->subject);

        $proxy->getForwarder()->registerExceptionHandler(function (ExecutionException $exception) {
            $exception->setShouldThrow(false);
        });

        $proxy->methodWithException();

        $this->assertTrue(true); // No exception was thrown
    }

    public function test_interactions_will_be_retrieved_from_cache_when_cached()
    {
        $proxy = new Proxy($this->subject);

        $retrieved = false;

        $proxy->setCache(new class($retrieved) extends Cache {

            private bool $retrieved;

            public function __construct(bool &$retrieved)
            {
                $this->retrieved = &$retrieved;
            }

            public function get(Interacts $interaction): ConcludedInteraction
            {
                $this->retrieved = true;

                return parent::get($interaction);
            }
        });

        $proxy->method(); // First call

        $proxy->method(); // Should be retrieved from cache

        $this->assertTrue($retrieved);
    }

    public function test_interactions_are_logged()
    {
        $proxy = new Proxy($this->subject);

        $proxy->property = 'new value';
        $proxy->method();
        $proxy->property = 'another value';
        $proxy->method();

        $this->assertCount(4, $proxy->getHistory());
    }

    public function test_result_is_wrapped_in_proxy_when_value_is_object()
    {
        $proxy = new Proxy($this->subject);

        $proxy->setDecorateMode(DecorateMode::ALWAYS);

        $result = $proxy->getChild();

        $this->assertInstanceOf(Proxy::class, $result);
    }

}