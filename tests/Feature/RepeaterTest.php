<?php

namespace Test\Feature;

use JesseGall\Proxy\Implementations\Handlers\Exceptions\MaxAttemptsExceededException;
use JesseGall\Proxy\Implementations\Handlers\Repeater;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Strategies\ForwardStrategy;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestException;

class RepeaterTest extends TestCase
{

    public function test_repeats_expected_amount_of_times()
    {
        $repeater = new Repeater($maxAttempts = 10);

        $strategy = $this->createMock(ForwardStrategy::class);

        $strategy->method('execute')->willThrowException(
            $exception = new ExecutionException($strategy, new TestException())
        );

        $strategy->expects($this->exactly($maxAttempts))->method('execute');

        try {
            $repeater->handle($exception);
        } catch (MaxAttemptsExceededException $e) {
            //
        }
    }

    public function test_throws_exception_when_max_attempts_exceeded_and()
    {
        $repeater = new Repeater();

        $strategy = $this->createMock(ForwardStrategy::class);

        $strategy->method('execute')->willThrowException(
            $exception = new ExecutionException($strategy, new TestException())
        );

        $this->expectException(MaxAttemptsExceededException::class);

        try {
            $repeater->handle($exception);
        } catch (MaxAttemptsExceededException $exception) {
            $this->assertEquals($strategy, $exception->getException()->getStrategy());

            throw $exception;
        }
    }

    public function test_handle_returns_when_execution_succeeds()
    {
        $repeater = new Repeater();

        $strategy = $this->createMock(ForwardStrategy::class);

        $strategy->expects($this->once())->method('execute');

        $repeater->handle(new ExecutionException($strategy, new TestException()));
    }

}