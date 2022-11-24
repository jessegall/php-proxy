<?php

namespace Tests\TestClasses;

class TestSubject
{

    public string $property = 'value';

    public int $called = 0;

    public function method(): string
    {
        $this->called++;

        return 'value';
    }

    public function methodWithException(): void
    {
        $this->called++;

        throw new TestException();
    }

    public function getChild(): TestSubject
    {
        return new TestSubject();
    }

}