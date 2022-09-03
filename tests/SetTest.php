<?php

namespace Test;

use JesseGall\Proxy\Interactions\Set;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestTarget;

class SetTest extends TestCase
{

    public function test_set_and_get_property_change_and_return_expected_value()
    {
        $get = new Set(new TestTarget(), 'initialProperty', 'value');

        $get->setProperty($expected = 'newProperty');

        $this->assertEquals($expected, $get->getProperty());
    }

    public function test_set_and_get_value_change_and_return_expected_value()
    {
        $get = new Set(new TestTarget(), 'property', 'initialValue');

        $get->setValue($expected = 'newValue');

        $this->assertEquals($expected, $get->getValue());
    }

}