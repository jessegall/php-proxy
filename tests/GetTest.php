<?php

namespace Test;

use JesseGall\Proxy\Interactions\Get;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestTarget;

class GetTest extends TestCase
{

    public function test_set_and_get_property_change_and_return_expected_value()
    {
        $get = new Get(new TestTarget(), 'initialProperty');

        $get->setProperty($expected = 'newProperty');

        $this->assertEquals($expected, $get->getProperty());
    }

}