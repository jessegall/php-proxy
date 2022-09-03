<?php

namespace Test;

use InvalidArgumentException;
use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\ExceptionHandler;
use JesseGall\Proxy\FailedExecution;
use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Get;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Set;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\ForwardCall;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestException;
use Test\TestClasses\TestForwarder;
use Test\TestClasses\TestForwardStrategy;
use Test\TestClasses\TestInteraction;
use Test\TestClasses\TestInterceptor;
use Test\TestClasses\TestTarget;

class ForwarderTest extends TestCase
{

    public function test_call_is_forwarded_with_correct_parameters()
    {
        $forwarder = new TestForwarder();

        $target = $this->createMock(TestTarget::class);

        $target->expects($this->once())->method('call')->with(
            1, 2, 3
        );

        $forwarder->forward(new Call($target, 'call', [1, 2, 3]));
    }

    public function test_get_is_forwarded_and_returns_expected_value()
    {
        $forwarder = new TestForwarder();

        $target = new TestTarget();

        $actual = $forwarder->forward(new Get($target, 'get'))->getResult();

        $this->assertEquals('expected', $actual);
    }

    public function test_set_is_forwarded_and_sets_expected_value()
    {
        $forwarder = new TestForwarder();

        $target = new TestTarget();

        $forwarder->forward(new Set($target, 'set', 'some new value'));

        $this->assertEquals('some new value', $target->set);
    }

    public function test_get_set_interceptors_set_and_return_expected_value()
    {
        $forwarder = new TestForwarder();

        $forwarder->setInterceptors($interceptors = [new class implements Intercepts {

            public function intercept(Interaction $interaction): void
            {
                //
            }

        }]);

        $this->assertEquals($interceptors, $forwarder->getInterceptors());
    }

    public function test_interceptor_can_be_registered()
    {
        $forwarder = new TestForwarder();

        $forwarder->registerInterceptor(new class implements Intercepts {

            public function intercept(Interaction $interaction): void
            {
                //
            }

        });

        $this->assertCount(1, $forwarder->getInterceptors());
    }

    public function test_interceptor_can_be_registered_with_class_string()
    {
        $forwarder = new TestForwarder();

        $forwarder->registerInterceptor(TestInterceptor::class);

        $this->assertCount(1, $forwarder->getInterceptors());
    }

    public function test_multiple_interceptors_can_be_registered_with_an_array_of_class_strings()
    {
        $forwarder = new TestForwarder();

        $forwarder->registerInterceptor([
            TestInterceptor::class,
            TestInterceptor::class,
            TestInterceptor::class,
        ]);

        $this->assertCount(3, $forwarder->getInterceptors());
    }

    public function test_an_exception_is_thrown_when_registering_interceptor_with_invalid_class_type()
    {
        $forwarder = new TestForwarder();

        $this->expectException(InvalidArgumentException::class);

        $forwarder->registerInterceptor(TestTarget::class);
    }

    public function test_when_exception_is_thrown_the_handler_is_called()
    {
        $forwarder = new TestForwarder();

        $forwarder->setExceptionHandler($handler = $this->createMock(ExceptionHandler::class));

        $strategy = new TestForwardStrategy(
            new TestInteraction(new TestTarget(), fn() => throw new TestException())
        );

        $handler->expects($this->once())
            ->method('handle')
            ->with(new FailedExecution($strategy, new TestException()));

        $forwarder->try($strategy);
    }

    public function test_when_interceptor_sets_interaction_to_fulfilled_the_interaction_does_not_forward_to_target()
    {
        $forwarder = $this->createMock(TestForwarder::class);

        $forwarder->registerInterceptor(new class implements Intercepts {

            public function intercept(Interaction $interaction): void
            {
                $interaction->setStatus(Status::FULFILLED);
            }

        });

        $forwarder->forward(new Get(new TestTarget(), 'get'));

        $forwarder->expects($this->never())->method('newForwardStrategy');
    }

    public function test_when_interceptor_sets_interaction_to_cancelled_the_interaction_does_not_forward_to_target()
    {
        $forwarder = $this->createMock(TestForwarder::class);

        $forwarder->registerInterceptor(new class implements Intercepts {

            public function intercept(Interaction $interaction): void
            {
                $interaction->setStatus(Status::CANCELLED);
            }

        });

        $forwarder->forward(new Get(new TestTarget(), 'get'));

        $forwarder->expects($this->never())->method('newForwardStrategy');
    }

    public function test_when_interceptor_sets_status_to_fulfilled_and_sets_result_the_expected_value_is_returned()
    {
        $forwarder = new TestForwarder();

        $forwarder->registerInterceptor(new class implements Intercepts {

            public function intercept(Interaction $interaction): void
            {
                $interaction->setStatus(Status::FULFILLED)->setResult('expected');
            }

        });

        $concluded = $forwarder->forward(new Get(new TestTarget(), 'get'));

        $this->assertEquals(
            'expected',
            $concluded->getResult()
        );
    }

    public function test_null_is_returned_when_target_does_not_return_a_value()
    {
        $forwarder = new TestForwarder();

        $concluded = $forwarder->forward(new Call(new class {
            public function empty()
            {
                // Return nothing
            }
        }, 'empty', []));

        $this->assertTrue($concluded->hasStatus(Status::FULFILLED));

        $this->assertNull($concluded->getResult());
    }

    public function test_concluded_interaction_has_correct_timestamp()
    {
        $forwarder = new TestForwarder();

        $concluded = $forwarder->forward(new Get(new TestTarget(), 'get'));

        $this->assertEquals(
            round(microtime(true)),
            round($concluded->getTimestamp()),
        );
    }

    public function test_setting_a_strategy_can_be_used_to_modify_the_result_of_an_interaction()
    {
        $forwarder = new TestForwarder();

        $forwarder->setStrategy(Call::class, ForwardCallValueChanger::class);

        $interaction = new Call(new TestTarget(), 'call', []);

        $concluded = $forwarder->forward($interaction);

        $this->assertEquals(
            'expected_expected',
            $concluded->getResult()
        );
    }

}

class ForwardCallValueChanger extends ForwardCall
{

    public function doExecute(): mixed
    {
        $result = parent::doExecute();

        return $result . '_' . $result;
    }

}