<?php

namespace Test\Unit;

use JesseGall\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestForwarder;
use Test\TestClasses\TestTarget;

class ProxyTest extends TestCase
{

    private Proxy $proxy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proxy = new Proxy(new TestTarget());
    }

    public function test_can_get_and_set_forwarder()
    {
        $this->proxy->setForwarder($expected = new TestForwarder());

        $this->assertEquals($expected, $this->proxy->getForwarder());
    }

    public function test_can_get_and_set_target()
    {
        $this->proxy->setTarget($expected = new TestTarget());

        $this->assertEquals($expected, $this->proxy->getTarget());
    }

    public function test_can_get_and_set_parent()
    {
        $this->proxy->setParent($expected = new Proxy(new TestTarget()));

        $this->assertEquals($expected, $this->proxy->getParent());
    }

}