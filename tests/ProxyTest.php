<?php

namespace Test;

use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\ExceptionHandler;
use JesseGall\Proxy\FailedAction;
use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Get;
use JesseGall\Proxy\Interactions\Set;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestException;
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
                (new Call($proxy->getTarget(), 'call', []))
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
                (new Get($proxy->getTarget(), 'get'))
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
                (new Set($proxy->getTarget(), 'set', 'new value'))
                    ->setStatus(Status::FULFILLED)
            ));

        $proxy->set = 'new value';
    }

    public function test_when_exception_is_thrown_the_handler_is_called()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setExceptionHandler($handler = $this->createMock(ExceptionHandler::class));

        $handler->expects($this->once())
            ->method('handle')
            ->with(new FailedAction(new TestException(), fn() => 0, $proxy));

        $proxy->call(fn() => throw new TestException());
    }

    public function test_result_is_decorated_when_value_is_an_object()
    {
        $proxy = new class(new TestTarget()) extends Proxy {

            protected function forwardCall(string $method, array $parameters): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new Call($this->target, 'call', []))->setResult(new \stdClass())
                );
            }

        };

        $result = $proxy->call(fn() => new \stdClass());

        $this->assertInstanceOf(
            Proxy::class,
            $result,
        );
    }

    public function test_cloned_proxy_is_equal_except_for_target()
    {
        $proxy = new class(new TestTarget()) extends Proxy {
            public function decorateObject(object $object): Proxy
            {
                return parent::decorateObject($object);
            }
        };

        $clone = $proxy->decorateObject(new \stdClass());

        $proxy->setTarget($clone->getTarget()); // To make the target equal

        $this->assertEquals($proxy, $clone);
    }

    public function test_interaction_is_registered_when_concluded()
    {
        $proxy = new class(new TestTarget()) extends Proxy {
            protected function forwardCall(string $method, array $parameters): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new Call($this->target, 'call', []))->setResult(new \stdClass())
                );
            }

            protected function forwardGet(string $property): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new Get($this->target, 'get'))->setResult(new \stdClass())
                );
            }

            protected function forwardSet(string $property, mixed $value): ConcludedInteraction
            {
                return new ConcludedInteraction(
                    (new Set($this->target, 'set', $value))
                );
            }
        };

        $proxy->call();
        $proxy->set = 'new value';
        $proxy->get;

        $this->assertCount(3, $proxy->getConcludedInteractions());
    }

    public function test_get_exception_handler_returns_expected_value()
    {
        $proxy = new Proxy(new TestTarget());

        $proxy->setExceptionHandler($handler = new ExceptionHandler());

        $this->assertEquals($handler, $proxy->getExceptionHandler());
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