<?php

use JesseGall\Proxy\Exceptions\MaxAttemptsExceededException;
use JesseGall\Proxy\ProxyRepeater;
use PHPUnit\Framework\TestCase;

class ProxyRepeaterTest extends TestCase
{

    public function test_max_attempts_exceeded_exception_is_thrown_when_max_attempts_is_exceeded(): void
    {
        $subject = new class {

            public function test(): void
            {
                throw new Exception();
            }

        };

        $proxy = new ProxyRepeater($subject, function () {
            return true; // Retry
        }, 5);

        $this->expectException(MaxAttemptsExceededException::class);

        $proxy->test();
    }

    public function test_original_exception_is_thrown_when_handler_returns_false(): void
    {
        $subject = new class {

            public function test(): void
            {
                throw new TestException();
            }

        };

        $proxy = new ProxyRepeater($subject, function () {
            return false;
        }, 5);

        $this->expectException(TestException::class);

        $proxy->test();
    }

    public function test_expected_value_is_returned_when_forwarding_call(): void
    {
        $subject = new class {

            public function test(): string
            {
                return 'expected';
            }

        };

        $proxy = new ProxyRepeater($subject);

        $this->assertEquals(
            'expected',
            $proxy->test()
        );
    }

    public function test_proxy_returns_expected_value_when_forwarding_call(): void
    {
        $subject = new class {

            public function test(): string
            {
                return 'expected';
            }

        };

        $proxy = new ProxyRepeater($subject);

        $this->assertEquals(
            'expected',
            $proxy->test()
        );
    }

    public function test_proxy_returns_expected_value_when_accessing_property(): void
    {
        $subject = new class {

            public string $test = 'expected';

        };

        $proxy = new ProxyRepeater($subject);

        $this->assertEquals(
            'expected',
            $proxy->test
        );
    }

    public function test_proxy_returns_expected_value_when_forwarding_call_after_repeating(): void
    {
        $subject = new class {

            public int $calls = 0;

            public function test(): string
            {
                if ($this->calls < 2) {
                    throw new TestException();
                }

                return 'expected';
            }

        };

        $proxy = new ProxyRepeater($subject, function () use ($subject) {
            $subject->calls++;

            return true;
        }, 5);

        $this->assertEquals(
            'expected',
            $proxy->test()
        );
    }

    public function test_proxy_returns_expected_value_when_accessing_property_after_repeating(): void
    {
        $subject = new class {

            public int $calls = 0;

            public function __get(string $name)
            {
                if ($this->calls < 2) {
                    throw new TestException();
                }

                return 'expected';
            }

        };

        $proxy = new ProxyRepeater($subject, function () use ($subject) {
            $subject->calls++;

            return true;
        }, 5);

        $this->assertEquals(
            'expected',
            $proxy->test
        );
    }

    public function test_exception_is_returned_as_first_parameter_of_the_handler()
    {
        $subject = new class {

            public function test(): void
            {
                throw new TestException();
            }

        };

        $actual = null;

        $proxy = new ProxyRepeater($subject, function (...$args) use (&$actual) {
            $actual = $args[0];

            return true;
        }, 2);

        try {
            $proxy->test();
        } catch (Exception) {
            //
        }

        $this->assertInstanceOf(
            TestException::class,
            $actual
        );
    }

    public function test_proxy_repeater_is_returned_as_second_parameter_of_the_handler()
    {
        $subject = new class {

            public function test(): void
            {
                throw new TestException();
            }

        };

        $actual = null;

        $proxy = new ProxyRepeater($subject, function (...$args) use (&$actual) {
            $actual = $args[1];

            return true;
        }, 2);

        try {
            $proxy->test();
        } catch (Exception) {
            //
        }

        $this->assertInstanceOf(
            ProxyRepeater::class,
            $actual
        );
    }

}

class TestException extends Exception
{

}