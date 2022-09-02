<?php

namespace Test;

use Exception;
use JesseGall\Proxy\Handlers\Catcher;
use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use stdClass;
use Test\TestClasses\TestException;

class CatcherTest extends TestCase
{

    public function test_get_handler_returns_current_handler()
    {
        $catcher = new Catcher($handler = fn() => 0);

        $this->assertEquals($handler, $catcher->getHandler());
    }

    public function test_given_exception_is_thrown_when_handler_is_not_set()
    {
        $catcher = new Catcher();

        $this->expectException(TestException::class);

        $catcher(new TestException(), fn() => 0, new Proxy(new stdClass()));
    }

    public function test_exception_is_ignored_and_expected_value_from_handler_is_returned()
    {
        $catcher = new Catcher(fn() => 'expected');

        try {
            $actual = $catcher(new Exception(), fn() => 0, new Proxy(new stdClass()));
        } catch (Exception) {
            $actual = 'invalid';
        }

        $this->assertEquals('expected', $actual);
    }

}