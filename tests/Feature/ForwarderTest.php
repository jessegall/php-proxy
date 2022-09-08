<?php

namespace Test\Feature;

use JesseGall\Proxy\ExceptionHandler;
use JesseGall\Proxy\Forwarder;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestException;
use Test\TestClasses\TestForwarder;
use Test\TestClasses\TestForwardStrategy;
use Test\TestClasses\TestInteraction;
use Test\TestClasses\TestInteractionWithResult;

class ForwarderTest extends TestCase
{

    public function test_strategy_will_not_execute_when_interaction_status_is_not_pending_after_notifying_interceptors()
    {
        foreach (Status::cases() as $status) {
            if ($status === Status::PENDING) {
                continue;
            }

            $forwarder = $this->createPartialMock(TestForwarder::class, ['newStrategy']);

            $forwarder->method('newStrategy')->willReturn(
                $strategy = $this->createMock(TestForwardStrategy::class)
            );

            $strategy->expects($this->never())->method('execute');

            $forwarder->registerInterceptor(
                fn(Interacts $interaction) => $interaction->setStatus($status)
            );

            $forwarder->forward(new TestInteraction());
        }
    }

    public function test_interaction_status_is_fulfilled_after_successfully_forwarding()
    {
        $forwarder = new Forwarder();

        $forwarder->setStrategy(TestInteraction::class, TestForwardStrategy::class);

        $concluded = $forwarder->forward(new TestInteraction());

        $this->assertEquals(Status::FULFILLED, $concluded->getStatus());
    }

    public function test_interaction_has_expected_result()
    {
        $forwarder = $this->createPartialMock(TestForwarder::class, ['newStrategy']);

        $forwarder->method('newStrategy')->willReturn(
            new TestForwardStrategy($interaction = new TestInteractionWithResult(), fn() => 'expected')
        );

        $concluded = $forwarder->forward($interaction);

        $this->assertEquals('expected', $concluded->getResult());
    }

    public function test_exception_is_correctly_passed_on_to_handler()
    {
        $forwarder = $this->createPartialMock(TestForwarder::class, ['newStrategy']);

        $forwarder->setExceptionHandler($handler = $this->createMock(ExceptionHandler::class));

        $forwarder->method('newStrategy')->willReturn(
            $strategy = new TestForwardStrategy($interaction = new TestInteraction())
        );

        $strategy->setDoExecute(fn() => throw new TestException());

        $handler->expects($this->once())->method('handle')->with(
            new ExecutionException($strategy, new TestException())
        );

        $forwarder->forward($interaction);
    }


}