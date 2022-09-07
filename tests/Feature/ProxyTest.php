<?php

namespace Test\Feature;

use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Interactions\SetInteraction;
use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestForwarder;
use Test\TestClasses\TestInteraction;
use Test\TestClasses\TestInteractionWithResult;
use Test\TestClasses\TestTarget;

class ProxyTest extends TestCase
{

    private Proxy $proxy;
    private TestTarget $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proxy = new Proxy($this->target = new TestTarget());
    }

    public function test_call_interaction_returns_expect_value()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            new ConcludedInteraction(new TestInteractionWithResult($expected = 'expected'))
        );

        $this->assertEquals($expected, $this->proxy->call());
    }

    public function test_get_interaction_returns_expect_value()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            new ConcludedInteraction(new TestInteractionWithResult($expected = 'expected'))
        );

        $this->assertEquals($expected, $this->proxy->property);
    }

    public function test_call_interaction_is_correctly_forwarded_to_forwarder()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->expects($this->once())->method('forward')->with(
            new CallInteraction($this->target, 'call', [1, 2, 3])
        );

        $this->proxy->call(1, 2, 3);
    }

    public function test_get_interaction_is_correctly_forwarded_to_forwarder()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->expects($this->once())->method('forward')->with(
            new GetInteraction($this->target, 'property')
        );

        $this->proxy->property;
    }

    public function test_set_interaction_is_correctly_forwarded_to_forwarder()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->expects($this->once())->method('forward')->with(
            new SetInteraction($this->target, 'property', 'value')
        );

        $this->proxy->property = 'value';
    }

    public function test_call_interaction_is_logged()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            $interaction = new ConcludedInteraction(new TestInteraction())
        );

        $this->proxy->call();

        $this->assertContains($interaction, $this->proxy->getConcludedInteractions());
    }

    public function test_get_interaction_is_logged()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            $interaction = new ConcludedInteraction(new TestInteraction())
        );

        $this->proxy->property;

        $this->assertContains($interaction, $this->proxy->getConcludedInteractions());
    }

    public function test_set_interaction_is_logged()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            $interaction = new ConcludedInteraction(new TestInteraction())
        );

        $this->proxy->property = 'value';

        $this->assertContains($interaction, $this->proxy->getConcludedInteractions());
    }

    public function test_forwarding_call_returns_decorated_object_when_result_is_an_object()
    {
        $this->proxy->setForwarder($forwarder = $this->createMock(TestForwarder::class));

        $forwarder->method('forward')->willReturn(
            new ConcludedInteraction(new TestInteractionWithResult($result = new \stdClass()))
        );

        $actual = $this->proxy->call();

        $this->assertInstanceOf(Proxy::class, $actual);

        $this->assertEquals($result, $actual->getTarget());
    }

}