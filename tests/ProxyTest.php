<?php

namespace Tests;

use JesseGall\Proxy\DecorateMode;
use JesseGall\Proxy\Forwarder\Exceptions\StrategyNullException;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;
use JesseGall\Proxy\Forwarder\StrategyFactory;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Proxy;
use Tests\TestClasses\TestException;
use Tests\TestClasses\TestTarget;

class ProxyTest extends TestCase
{

    private TestTarget $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new TestTarget();
    }

    public function test_can_get_property_through_proxy()
    {
        $proxy = new Proxy($this->target);

        $this->assertEquals('value', $proxy->property);
    }

    public function test_can_set_property_through_proxy()
    {
        $proxy = new Proxy($this->target);

        $proxy->property = 'new value';

        $this->assertEquals('new value', $this->target->property);
    }

    public function test_can_invoke_method_through_proxy()
    {
        $proxy = new Proxy($this->target);

        $this->assertEquals('value', $proxy->method());
    }

    public function test_interceptor_will_intercept_call_interaction()
    {
        $proxy = new Proxy($this->target);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->method();

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_will_intercept_get_interaction()
    {
        $proxy = new Proxy($this->target);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->property;

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_will_intercept_set_interaction()
    {
        $proxy = new Proxy($this->target);

        $intercepted = false;

        $proxy->getForwarder()->registerInterceptor(function () use (&$intercepted) {
            $intercepted = true;
        });

        $proxy->property = 'new value';

        $this->assertTrue($intercepted);
    }

    public function test_interceptor_can_cancel_interaction()
    {
        $proxy = new Proxy($this->target);

        $proxy->getForwarder()->registerInterceptor(function (Interacts $interaction) {
            $interaction->setStatus(Status::CANCELLED);
        });

        $proxy->method();

        $this->assertEquals(0, $this->target->called);
    }

    public function test_exception_handler_will_catch_exception()
    {
        $proxy = new Proxy($this->target);

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
        $proxy = new Proxy($this->target);

        $proxy->getForwarder()->registerExceptionHandler(function (ExecutionException $exception) {
            $exception->setShouldThrow(false);
        });

        $proxy->methodWithException();

        $this->assertTrue(true); // No exception was thrown
    }

    public function test_interactions_will_be_retrieved_from_cache_when_cached()
    {
        $proxy = new Proxy($this->target);

        $proxy->setCacheEnabled(true);

        $proxy->method(); // First call

        $proxy->method(); // Should be retrieved from cache

        $history = $proxy->getHistory();

        $this->assertFalse($history[0]->isFromCache());

        $this->assertTrue($history[1]->isFromCache());
    }

    public function test_interactions_will_not_be_cached_when_cache_is_disabled()
    {
        $proxy = new Proxy($this->target);

        $proxy->setCacheEnabled(false);

        $proxy->method();

        $interaction = $proxy->getHistory()[0]->getInteraction();

        $this->assertEmpty($proxy->getCacheHandler()->has($interaction));
    }

    public function test_interactions_are_logged()
    {
        $proxy = new Proxy($this->target);

        $proxy->property = 'new value';
        $proxy->method();
        $proxy->property = 'another value';
        $proxy->method();

        $this->assertCount(4, $proxy->getHistory());
    }

    public function test_result_is_wrapped_in_proxy_when_value_is_object()
    {
        $proxy = new Proxy($this->target);

        $proxy->setDecorateMode(DecorateMode::ALWAYS);

        $result = $proxy->getChild();

        $this->assertInstanceOf(Proxy::class, $result);
    }

    public function test_caller_is_correctly_set()
    {
        $proxy = new Proxy($this->target);

        $proxy->method();

        $interaction = $proxy->getHistory()[0];

        $this->assertSame($this, $interaction->getCaller());
    }

    public function test_exception_is_thrown_when_no_strategy_is_found()
    {
        $proxy = new Proxy($this->target);

        $proxy->getForwarder()->setFactory(new class extends StrategyFactory {
            public function make(Interacts $interaction, object $caller = null): ?Strategy
            {
                return null;
            }
        });

        $this->expectException(StrategyNullException::class);

        $proxy->method();
    }

    public function test_registered_listeners_are_called_when_interaction_is_concluded()
    {
        $proxy = new Proxy($this->target);

        $called = false;

        $proxy->registerListener(function () use (&$called) {
            $called = true;
        });

        $proxy->method();

        $this->assertTrue($called);
    }

}