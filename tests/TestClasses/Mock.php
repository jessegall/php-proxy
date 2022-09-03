<?php

namespace Test\TestClasses;

use Test\Concerns\LogsMethodCalls;

/**
 * @template T
 * @mixin T
 */
class Mock
{
    use LogsMethodCalls;

    private object $subject;

    public function __construct(object $subject)
    {
        $this->subject = $subject;
    }

    public function __call(string $method, array $parameters)
    {
        $this->logMethodCall($method);

        return $this->subject->{$method}(...$parameters);
    }

    public function __get(string $property)
    {
        return $this->subject->{$property};
    }

    public function __set(string $property, mixed $value)
    {
        $this->subject->{$property} = $value;
    }

}