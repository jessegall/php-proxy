<?php

namespace Test\TestClasses;

class TestException extends \Exception
{

    public function __construct(string $message = "This is a test exception")
    {
        parent::__construct($message);
    }

}