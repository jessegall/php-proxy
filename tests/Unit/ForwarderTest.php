<?php

namespace Test\Unit;

use JesseGall\Proxy\Exceptions\ForwardStrategyMissingException;
use JesseGall\Proxy\Forwarder;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestExceptionHandler;
use Test\TestClasses\TestForwarder;
use Test\TestClasses\TestForwardStrategy;
use Test\TestClasses\TestInteraction;
use Test\TestClasses\TestInterceptor;

class ForwarderTest extends TestCase
{

    private Forwarder $forwarder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->forwarder = new TestForwarder();
    }

    public function test_interceptor_can_be_registered()
    {
        $this->forwarder->registerInterceptor($expected = new TestInterceptor());

        $this->assertContains($expected, $this->forwarder->getInterceptors());
    }

    public function test_interceptor_can_be_registered_with_class_string()
    {
        $this->forwarder->registerInterceptor(TestInterceptor::class);

        $this->assertCount(1, $this->forwarder->getInterceptors());
    }

    public function test_interceptor_can_be_registered_with_an_array_of_class_strings()
    {
        $this->forwarder->registerInterceptor([
            TestInterceptor::class,
            TestInterceptor::class,
            TestInterceptor::class
        ]);

        $this->assertCount(3, $this->forwarder->getInterceptors());
    }

    public function test_interceptor_can_be_registered_with_a_closure()
    {
        $this->forwarder->registerInterceptor(fn() => null);

        $this->assertCount(1, $this->forwarder->getInterceptors());
    }

    public function test_expected_strategy_is_returned()
    {
        $this->forwarder->setStrategy(TestInteraction::class, TestForwardStrategy::class);

        $actual = $this->forwarder->newStrategy(new TestInteraction());

        $this->assertInstanceOf(TestForwardStrategy::class, $actual);
    }

    public function test_exception_is_thrown_when_strategy_is_missing()
    {
        $this->expectException(ForwardStrategyMissingException::class);

        $this->forwarder->newStrategy(new TestInteraction());
    }

    public function test_can_get_and_set_interceptors()
    {
        $this->forwarder->setInterceptors($expected = [new TestInterceptor()]);

        $this->assertEquals($expected, $this->forwarder->getInterceptors());
    }

    public function test_can_get_and_set_strategies()
    {
        $this->forwarder->setStrategies($expected = [TestInteraction::class => TestForwardStrategy::class]);

        $this->assertEquals($expected, $this->forwarder->getStrategies());
    }

    public function test_can_get_and_set_forward_strategy()
    {
        $this->forwarder->setStrategy(TestInteraction::class, $expected = TestForwardStrategy::class);

        $this->assertEquals($expected, $this->forwarder->getStrategy(TestInteraction::class));
    }

    public function test_can_clear_interceptors()
    {
        $this->forwarder->setInterceptors([
            new TestInterceptor()
        ]);

        $this->forwarder->clearInterceptors();

        $this->assertEmpty($this->forwarder->getInterceptors());
    }

    public function test_exception_handler_can_be_registered()
    {
        $this->forwarder->registerExceptionHandler(new TestExceptionHandler());

        $this->assertCount(1, $this->forwarder->getExceptionHandlers());
    }

    public function test_exception_handlers_can_be_registered_with_and_array_of_exception_handlers()
    {
        $this->forwarder->registerExceptionHandler([
            new TestExceptionHandler(),
            new TestExceptionHandler()
        ]);

        $this->assertCount(2, $this->forwarder->getExceptionHandlers());
    }

    public function test_can_clear_exception_handlers()
    {
        $this->forwarder->setExceptionHandlers([
            new TestExceptionHandler()
        ]);

        $this->forwarder->clearExceptionHandlers();

        $this->assertCount(0, $this->forwarder->getExceptionHandlers());
    }

    public function test_can_get_and_set_exception_handlers()
    {
        $this->forwarder->setExceptionHandlers($expected = [new TestExceptionHandler()]);

        $this->assertEquals($expected, $this->forwarder->getExceptionHandlers());
    }

}