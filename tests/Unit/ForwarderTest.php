<?php

namespace Test\Unit;

use JesseGall\Invader\Invader;
use JesseGall\Proxy\ClosureInterceptor;
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

        $this->assertContains(
            TestInterceptor::class,
            array_map(fn($i) => get_class($i), $this->forwarder->getInterceptors())
        );
    }

    public function test_interceptor_can_be_registered_with_an_array_of_class_strings()
    {
        $this->forwarder->registerInterceptor([
            TestInterceptor::class,
            TestInterceptor::class,
            TestInterceptor::class
        ]);

        $this->assertCount(3, $this->forwarder->getInterceptors());

        foreach ($this->forwarder->getInterceptors() as $interceptor) {
            $this->assertEquals(TestInterceptor::class, get_class($interceptor));
        }
    }

    public function test_interceptor_can_be_registered_with_a_closure()
    {
        $this->forwarder->registerInterceptor($closure = function () { });

        $this->assertCount(1, $this->forwarder->getInterceptors());

        $interceptor = $this->forwarder->getInterceptors()[0];

        $this->assertInstanceOf(ClosureInterceptor::class, $interceptor);

        $this->assertEquals($closure, $interceptor->getClosure());
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
        $forwarder = invade($this->forwarder);
g
        $forwarder->interceptors = [
            new TestInterceptor()
        ];

        $this->forwarder->clearInterceptors();

        $this->assertEmpty($forwarder->interceptors);
    }

}