<?php

namespace Test\Feature;

use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Interactions\SetInteraction;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestTarget;

class ProxyTest extends TestCase
{

    public function test_call_is_forwarded_to_forwarder_and_expected_value_is_returned()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setForwarder($forwarder = $this->createMock(Forwarder::class));

        $forwarder->expects($this->once())
            ->method('forward')
            ->willReturn(new ConcludedInteraction(
                (new CallInteraction($proxy->getTarget(), 'call', []))
                    ->setStatus(Status::FULFILLED)
                    ->setResult('expected')
            ));

        $this->assertEquals(
            'expected',
            $proxy->call()
        );
    }

    public function test_get_is_forwarded_to_forwarder_and_expected_value_is_returned()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setForwarder($forwarder = $this->createMock(Forwarder::class));

        $forwarder->expects($this->once())
            ->method('forward')
            ->willReturn(new ConcludedInteraction(
                (new GetInteraction($proxy->getTarget(), 'get'))
                    ->setStatus(Status::FULFILLED)
                    ->setResult('expected')
            ));

        $this->assertEquals(
            'expected',
            $proxy->test
        );
    }

    public function test_set_is_forwarded_to_forwarder_and_expected_value_is_returned()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setForwarder($forwarder = $this->createMock(Forwarder::class));

        $forwarder->expects($this->once())
            ->method('forward')
            ->willReturn(new ConcludedInteraction(
                (new SetInteraction($proxy->getTarget(), 'set', 'new value'))
                    ->setStatus(Status::FULFILLED)
            ));

        $proxy->set = 'new value';
    }

    public function test_result_is_decorated_when_value_is_an_object()
    {
        $proxy = new class(new TestTarget()) extends Proxy {

            protected function forwardCall(string $method, array $parameters): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new CallInteraction($this->target, 'call', []))->setResult(new \stdClass())->setStatus(Status::FULFILLED)
                );
            }

        };

        $result = $proxy->call(fn() => new \stdClass());

        $this->assertInstanceOf(
            Proxy::class,
            $result,
        );
    }

    public function test_new_proxy_parent_is_correctly_set_when_decorating_object()
    {
        $proxy = new class(new TestTarget()) extends Proxy {
            public function decorateObject(object $object): Proxy
            {
                return parent::decorateObject($object);
            }
        };

        $newProxy = $proxy->decorateObject(new \stdClass());

        $this->assertEquals($proxy->getForwarder(), $newProxy->getForwarder());
    }

    public function test_new_proxy_has_same_forwarder_as_parent()
    {
        $proxy = new class(new TestTarget()) extends Proxy {
            public function decorateObject(object $object): Proxy
            {
                return parent::decorateObject($object);
            }
        };

        $newProxy = $proxy->decorateObject(new \stdClass());

        $this->assertEquals($proxy->getForwarder(), $newProxy->getForwarder());
    }

    public function test_interactions_are_logged_when_concluded()
    {
        $proxy = new class(new TestTarget()) extends Proxy {
            protected function forwardCall(string $method, array $parameters): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new CallInteraction($this->target, 'call', []))->setResult(new \stdClass())
                );
            }

            protected function forwardGet(string $property): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new GetInteraction($this->target, 'get'))->setResult(new \stdClass())
                );
            }

            protected function forwardSet(string $property, mixed $value): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new SetInteraction($this->target, 'set', $value))
                );
            }
        };

        $proxy->call();
        $proxy->set = 'new value';
        $proxy->get;

        $this->assertCount(3, $proxy->getConcludedInteractions());
    }

    public function test_get_target_returns_expected_value()
    {
        $proxy = new Proxy($target = new TestTarget());

        $this->assertEquals($target, $proxy->getTarget());
    }

    public function test_get_forwarder_returns_expected_value()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setForwarder($forwarder = new Forwarder());

        $this->assertEquals($forwarder, $proxy->getForwarder());
    }

}